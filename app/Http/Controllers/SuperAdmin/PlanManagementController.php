<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanManagementController extends Controller
{
    /**
     * List all subscription plans
     */
    public function index(Request $request)
    {
        $query = Plan::withCount('subscriptions');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->orderBy('sort_order')->orderBy('id')->get();

        return view('super-admin.plans.index', compact('plans'));
    }

    /**
     * Show plan creation form
     */
    public function create()
    {
        return view('super-admin.plans.create');
    }

    /**
     * Store new plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:plans,slug'],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'max_meters' => ['required', 'integer', 'min:100'],
            'max_workers' => ['required', 'integer', 'min:1'],
            'price_1m' => ['required', 'numeric', 'min:0'],
            'price_3m' => ['required', 'numeric', 'min:0'],
            'price_6m' => ['required', 'numeric', 'min:0'],
            'price_1y' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'string'],
            'status' => ['required', 'in:active,promo,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->input('features', ''))));

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        // Ensure slug is unique
        $baseSlug = $slug;
        $counter = 1;
        while (Plan::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        Plan::create([
            'name' => $request->name,
            'slug' => $slug,
            'badge' => $request->badge,
            'description' => $request->description,
            'max_meters' => $request->max_meters,
            'max_workers' => $request->max_workers,
            'price_1m' => $request->price_1m,
            'price_3m' => $request->price_3m,
            'price_6m' => $request->price_6m,
            'price_1y' => $request->price_1y,
            'features' => array_values($features),
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        return redirect()->route('super-admin.plans.index')
            ->with('success', "Plan '{$request->name}' created successfully.");
    }

    /**
     * Show plan edit form
     */
    public function edit(Plan $plan)
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    /**
     * Update existing plan
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:plans,slug,'.$plan->id],
            'badge' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'max_meters' => ['required', 'integer', 'min:100'],
            'max_workers' => ['required', 'integer', 'min:1'],
            'price_1m' => ['required', 'numeric', 'min:0'],
            'price_3m' => ['required', 'numeric', 'min:0'],
            'price_6m' => ['required', 'numeric', 'min:0'],
            'price_1y' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'string'],
            'status' => ['required', 'in:active,promo,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->input('features', ''))));

        $plan->update([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'badge' => $request->badge,
            'description' => $request->description,
            'max_meters' => $request->max_meters,
            'max_workers' => $request->max_workers,
            'price_1m' => $request->price_1m,
            'price_3m' => $request->price_3m,
            'price_6m' => $request->price_6m,
            'price_1y' => $request->price_1y,
            'features' => array_values($features),
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        return redirect()->route('super-admin.plans.index')
            ->with('success', "Plan '{$plan->name}' updated successfully.");
    }

    /**
     * Toggle plan status active/promo <-> archived
     */
    public function toggleStatus(Plan $plan)
    {
        $newStatus = ($plan->status === 'archived') ? 'active' : 'archived';
        $plan->update(['status' => $newStatus]);

        return back()->with('success', "Plan '{$plan->name}' is now {$newStatus}.");
    }
}
