<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exceptions\InsufficientWalletBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminWalletController extends Controller
{
    /**
     * List all agency wallet balances and recent transactions
     */
    public function index(Request $request)
    {
        $query = Company::withCount(['walletTransactions', 'referralsMade']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $companies = $query->orderBy('wallet_balance', 'desc')->paginate(15)->withQueryString();
        $totalPlatformWalletLiability = Company::sum('wallet_balance');
        $recentTransactions = WalletTransaction::with('company')->latest('created_at')->limit(10)->get();

        return view('super-admin.wallets.index', compact('companies', 'totalPlatformWalletLiability', 'recentTransactions'));
    }

    /**
     * Gift / adjust promotional credits
     */
    public function adjust(Request $request, Company $company, WalletService $walletService)
    {
        $request->validate([
            'type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $type = $request->type;
        $amount = (float) $request->amount;
        $reason = '[Admin Adjustment] '.$request->reason;
        $refId = 'ADJ-'.strtoupper(uniqid());

        try {
            if ($type === 'credit') {
                $walletService->credit($company, $amount, $reason, $refId, Auth::id());
                $msg = 'Successfully credited ₹'.number_format($amount, 2)." to '{$company->name}'.";
            } else {
                $walletService->debit($company, $amount, $reason, $refId, Auth::id());
                $msg = 'Successfully debited ₹'.number_format($amount, 2)." from '{$company->name}'.";
            }

            return back()->with('success', $msg);
        } catch (InsufficientWalletBalanceException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Wallet adjustment failed: '.$e->getMessage());
        }
    }

    /**
     * View specific company's transaction history passbook
     */
    public function history(Company $company)
    {
        $transactions = $company->walletTransactions()->paginate(20);

        return view('super-admin.wallets.history', compact('company', 'transactions'));
    }
}
