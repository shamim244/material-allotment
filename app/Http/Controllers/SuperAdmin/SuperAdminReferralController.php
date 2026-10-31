<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;

class SuperAdminReferralController extends Controller
{
    /**
     * Platform-wide referrals ledger
     */
    public function index(Request $request)
    {
        $query = Referral::with(['referrerCompany', 'referredCompany', 'walletTransaction']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $referrals = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_referrals' => Referral::count(),
            'rewarded' => Referral::where('status', 'rewarded')->count(),
            'pending' => Referral::where('status', 'pending')->count(),
            'total_paid_reward' => Referral::where('status', 'rewarded')->sum('reward_amount'),
        ];

        return view('super-admin.referrals.index', compact('referrals', 'stats'));
    }
}
