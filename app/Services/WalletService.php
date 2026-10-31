<?php

namespace App\Services;

use App\Exceptions\InsufficientWalletBalanceException;
use App\Models\Company;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    /**
     * Credit money into company's wallet atomically
     */
    public function credit(
        Company $company,
        float $amount,
        string $reason,
        ?string $referenceId = null,
        ?int $createdBy = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Credit amount must be greater than zero.');
        }

        $amount = round($amount, 2);

        return DB::transaction(function () use ($company, $amount, $reason, $referenceId, $createdBy) {
            /** @var Company $lockedCompany */
            $lockedCompany = Company::where('id', $company->id)->lockForUpdate()->firstOrFail();

            $newBalance = round((float) $lockedCompany->wallet_balance + $amount, 2);
            $lockedCompany->wallet_balance = $newBalance;
            $lockedCompany->save();

            // Refresh the in-memory model passed by caller
            $company->wallet_balance = $newBalance;

            return WalletTransaction::create([
                'company_id' => $lockedCompany->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'reference_id' => $referenceId,
                'created_by' => $createdBy,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Debit money from company's wallet atomically with balance guard
     */
    public function debit(
        Company $company,
        float $amount,
        string $reason,
        ?string $referenceId = null,
        ?int $createdBy = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Debit amount must be greater than zero.');
        }

        $amount = round($amount, 2);

        return DB::transaction(function () use ($company, $amount, $reason, $referenceId, $createdBy) {
            /** @var Company $lockedCompany */
            $lockedCompany = Company::where('id', $company->id)->lockForUpdate()->firstOrFail();

            $currentBalance = round((float) $lockedCompany->wallet_balance, 2);

            if ($currentBalance < $amount) {
                throw new InsufficientWalletBalanceException($currentBalance, $amount);
            }

            $newBalance = round($currentBalance - $amount, 2);
            $lockedCompany->wallet_balance = $newBalance;
            $lockedCompany->save();

            // Refresh the in-memory model passed by caller
            $company->wallet_balance = $newBalance;

            return WalletTransaction::create([
                'company_id' => $lockedCompany->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'reference_id' => $referenceId,
                'created_by' => $createdBy,
                'created_at' => now(),
            ]);
        });
    }
}
