<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Referral;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Subscribe contractor company to a plan with optional wallet balance deduction
     */
    public function subscribe(
        Company $company,
        Plan $plan,
        string $cycle,
        bool $useWallet = true,
        ?int $createdBy = null
    ): Subscription {
        if (! in_array($cycle, ['1m', '3m', '6m', '1y'])) {
            throw new \InvalidArgumentException("Invalid billing cycle '{$cycle}'. Allowed cycles are 1m, 3m, 6m, 1y.");
        }

        $grossPrice = $plan->priceForCycle($cycle);
        $durationDays = Plan::daysForCycle($cycle);

        return DB::transaction(function () use ($company, $plan, $cycle, $grossPrice, $durationDays, $useWallet, $createdBy) {
            // Lock company to get fresh wallet balance and avoid race conditions
            /** @var Company $lockedCompany */
            $lockedCompany = Company::where('id', $company->id)->lockForUpdate()->firstOrFail();

            $walletDiscount = 0.00;
            if ($useWallet && $lockedCompany->wallet_balance > 0) {
                $walletDiscount = min((float) $lockedCompany->wallet_balance, $grossPrice);
            }

            $amountPaid = round($grossPrice - $walletDiscount, 2);

            // Calculate start and end times (extend if active subscription exists)
            $currentSub = $lockedCompany->currentSubscription();
            if ($currentSub && $currentSub->ends_at->isFuture()) {
                $startsAt = $currentSub->ends_at;
                $endsAt = $currentSub->ends_at->copy()->addDays($durationDays);
            } else {
                $startsAt = now();
                $endsAt = now()->copy()->addDays($durationDays);
            }

            $subRef = 'SUB-'.strtoupper(Str::random(8));

            // Debit wallet if discount is applied
            if ($walletDiscount > 0) {
                $this->walletService->debit(
                    $lockedCompany,
                    $walletDiscount,
                    "Wallet payment for {$plan->name} ({$cycle})",
                    $subRef,
                    $createdBy
                );
            }

            // Create subscription record
            $subscription = Subscription::create([
                'company_id' => $lockedCompany->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'billing_cycle' => $cycle,
                'duration_days' => $durationDays,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'price' => $grossPrice,
                'wallet_discount' => $walletDiscount,
                'amount_paid' => $amountPaid,
                'payment_method' => ($walletDiscount >= $grossPrice) ? 'wallet' : 'manual',
                'status' => 'active',
                'max_meters' => $plan->max_meters,
                'max_workers' => $plan->max_workers,
                'is_custom_deal' => false,
                'custom_deal_notes' => null,
                'created_by' => $createdBy,
            ]);

            // Update company quota limits and active status
            $lockedCompany->max_meters = $plan->max_meters;
            $lockedCompany->max_workers = $plan->max_workers;
            $lockedCompany->status = 'active';
            $lockedCompany->save();

            // Check and reward referral if applicable
            $this->checkAndRewardReferral($lockedCompany);

            // Sync original company model
            $company->wallet_balance = $lockedCompany->wallet_balance;
            $company->max_meters = $lockedCompany->max_meters;
            $company->max_workers = $lockedCompany->max_workers;
            $company->status = $lockedCompany->status;

            return $subscription;
        });
    }

    /**
     * Create custom deal / override subscription for one-off deals without cluttering plans table
     */
    public function createCustomDeal(Company $company, array $data, ?int $createdBy = null): Subscription
    {
        return DB::transaction(function () use ($company, $data, $createdBy) {
            /** @var Company $lockedCompany */
            $lockedCompany = Company::where('id', $company->id)->lockForUpdate()->firstOrFail();

            $durationDays = (int) ($data['duration_days'] ?? 30);
            $price = round((float) ($data['price'] ?? 0.00), 2);
            $maxMeters = (int) ($data['max_meters'] ?? $lockedCompany->max_meters);
            $maxWorkers = (int) ($data['max_workers'] ?? $lockedCompany->max_workers);
            $notes = $data['notes'] ?? 'Super Admin Custom Deal Override';

            $plan = null;
            $planName = 'Custom Deal ('.$durationDays.' Days)';
            if (! empty($data['plan_id'])) {
                $plan = Plan::find($data['plan_id']);
                if ($plan) {
                    $planName = $plan->name.' (Custom Deal)';
                }
            }

            // Expiry calculation
            $currentSub = $lockedCompany->currentSubscription();
            if ($currentSub && $currentSub->ends_at->isFuture()) {
                $startsAt = $currentSub->ends_at;
                $endsAt = $currentSub->ends_at->copy()->addDays($durationDays);
            } else {
                $startsAt = now();
                $endsAt = now()->copy()->addDays($durationDays);
            }

            $subscription = Subscription::create([
                'company_id' => $lockedCompany->id,
                'plan_id' => $plan?->id,
                'plan_name' => $planName,
                'billing_cycle' => 'custom',
                'duration_days' => $durationDays,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'price' => $price,
                'wallet_discount' => 0.00,
                'amount_paid' => $price,
                'payment_method' => 'super_admin_override',
                'status' => 'active',
                'max_meters' => $maxMeters,
                'max_workers' => $maxWorkers,
                'is_custom_deal' => true,
                'custom_deal_notes' => $notes,
                'created_by' => $createdBy,
            ]);

            // Update company quota limits and active status
            $lockedCompany->max_meters = $maxMeters;
            $lockedCompany->max_workers = $maxWorkers;
            $lockedCompany->status = 'active';
            $lockedCompany->save();

            // Check and reward referral if applicable
            $this->checkAndRewardReferral($lockedCompany);

            $company->max_meters = $lockedCompany->max_meters;
            $company->max_workers = $lockedCompany->max_workers;
            $company->status = $lockedCompany->status;

            return $subscription;
        });
    }

    /**
     * Check if company has an unpaid referral and reward the referrer company
     */
    public function checkAndRewardReferral(Company $company): ?Referral
    {
        if (! $company->referred_by_id || $company->referred_by_id === $company->id) {
            return null;
        }

        $referral = Referral::where('referred_company_id', $company->id)->lockForUpdate()->first();

        // If referral doesn't exist yet, create it
        if (! $referral) {
            $referral = Referral::create([
                'referrer_company_id' => $company->referred_by_id,
                'referred_company_id' => $company->id,
                'reward_amount' => 500.00,
                'status' => 'pending',
            ]);
        }

        // Only reward once
        if ($referral->status === 'rewarded') {
            return $referral;
        }

        $referrer = Company::where('id', $referral->referrer_company_id)->lockForUpdate()->first();
        if (! $referrer) {
            return null;
        }

        // Credit referrer company's wallet
        $refId = 'REF-RWD-'.$company->id;
        $tx = $this->walletService->credit(
            $referrer,
            $referral->reward_amount,
            "Referral reward for contractor '{$company->name}' subscription activation",
            $refId
        );

        $referral->update([
            'status' => 'rewarded',
            'rewarded_at' => now(),
            'wallet_transaction_id' => $tx->id,
        ]);

        return $referral;
    }
}
