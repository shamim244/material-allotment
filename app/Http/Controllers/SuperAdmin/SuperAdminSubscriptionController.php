<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminSubscriptionController extends Controller
{
    /**
     * Show custom deal / override form for a specific company
     */
    public function createOverride(Company $company)
    {
        $currentSubscription = $company->currentSubscription();
        $plans = Plan::public()->get();

        return view('super-admin.subscriptions.override', compact('company', 'currentSubscription', 'plans'));
    }

    /**
     * Store custom deal / override subscription
     */
    public function storeOverride(Request $request, Company $company, SubscriptionService $subscriptionService)
    {
        $request->validate([
            'plan_id' => ['nullable', 'exists:plans,id'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_meters' => ['required', 'integer', 'min:100'],
            'max_workers' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $subscription = $subscriptionService->createCustomDeal(
            $company,
            $request->only(['plan_id', 'duration_days', 'price', 'max_meters', 'max_workers', 'notes']),
            Auth::id()
        );

        return redirect()->route('super-admin.companies.index')
            ->with('success', "Custom deal override granted to '{$company->name}'! Active until ".$subscription->ends_at->format('M d, Y').'.');
    }
}
