<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientWalletBalanceException;
use App\Models\Plan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Show Contractor Billing, Plans, Wallet & Referrals
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (! $company) {
            return redirect()->route('dashboard')->with('error', 'No company associated with this account.');
        }

        // Active / latest subscription
        $currentSubscription = $company->currentSubscription();
        $latestSubscription = $company->latestSubscription();

        // Quota usage for this specific company
        $usedMeters = $company->meters()->count();
        $usedWorkers = $company->workers()->count();

        // Available plans
        $plans = Plan::public()->orderBy('sort_order')->get();

        // Wallet transactions passbook
        $transactions = $company->walletTransactions()->paginate(10)->withQueryString();

        // Referrals
        $referrals = $company->referralsMade()->with('referredCompany')->latest()->get();
        $totalReferralRewards = $referrals->where('status', 'rewarded')->sum('reward_amount');

        return view('billing.index', compact(
            'company',
            'currentSubscription',
            'latestSubscription',
            'usedMeters',
            'usedWorkers',
            'plans',
            'transactions',
            'referrals',
            'totalReferralRewards'
        ));
    }

    /**
     * Subscribe to a plan or renew current plan
     */
    public function subscribe(Request $request, SubscriptionService $subscriptionService)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'cycle' => ['required', 'in:1m,3m,6m,1y'],
            'use_wallet' => ['nullable', 'boolean'],
        ]);

        $company = Auth::user()->company;
        if (! $company) {
            return back()->with('error', 'No company associated with this account.');
        }

        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->status === 'archived') {
            return back()->with('error', 'This subscription plan is archived and no longer available for subscription.');
        }

        $useWallet = $request->boolean('use_wallet', true);

        try {
            $subscription = $subscriptionService->subscribe(
                $company,
                $plan,
                $request->cycle,
                $useWallet,
                Auth::id()
            );

            $cycleText = Plan::cycleLabel($request->cycle);
            $msg = "Successfully subscribed to {$plan->name} ({$cycleText})! Plan valid until ".$subscription->ends_at->format('M d, Y').'.';

            return redirect()->route('billing.index')->with('success', $msg);
        } catch (InsufficientWalletBalanceException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Subscription failed: '.$e->getMessage());
        }
    }
}
