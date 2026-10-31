<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Dashboard overview
     */
    public function dashboard()
    {
        $stats = [
            'total_companies'     => Company::count(),
            'active_companies'    => Company::where('status', 'active')->count(),
            'suspended_companies' => Company::where('status', 'suspended')->count(),
            'total_contractors'   => User::where('role', 'contractor')->count(),
            'total_workers'       => User::where('role', 'worker')->count(),
            'total_meters'        => Meter::withoutGlobalScope('company')->count(),
            'installed_meters'    => Meter::withoutGlobalScope('company')->where('status', 'installed')->count(),
            'in_stock_meters'     => Meter::withoutGlobalScope('company')->where('status', 'stock')->count(),
        ];

        $companies = Company::withCount(['meters', 'workers', 'contractors'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super-admin.dashboard', compact('stats', 'companies'));
    }

    /**
     * List all companies
     */
    public function index(Request $request)
    {
        $query = Company::withCount(['meters', 'workers', 'contractors']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('super-admin.companies.index', compact('companies'));
    }

    /**
     * Show create new contractor company form
     */
    public function create()
    {
        return view('super-admin.companies.create');
    }

    /**
     * Store new contractor company & provision initial contractor account
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'code'           => ['nullable', 'string', 'max:50', 'unique:companies,code'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],
            'max_workers'    => ['required', 'integer', 'min:1'],
            'max_meters'     => ['required', 'integer', 'min:100'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:6'],
        ]);

        $company = Company::create([
            'name'           => $request->name,
            'code'           => $request->code ?: ('NCC-' . str_pad(Company::count() + 1, 3, '0', STR_PAD_LEFT)),
            'contact_person' => $request->contact_person,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'status'         => 'active',
            'max_workers'    => $request->max_workers,
            'max_meters'     => $request->max_meters,
        ]);

        // Create primary contractor admin user for this company
        User::create([
            'company_id'     => $company->id,
            'name'           => $request->admin_name,
            'email'          => $request->admin_email,
            'mobile'         => $request->phone,
            'password'       => Hash::make($request->admin_password),
            'plain_password' => $request->admin_password,
            'role'           => 'contractor',
            'is_active'      => true,
        ]);

        return redirect()->route('super-admin.companies.index')
            ->with('success', "Contractor '{$company->name}' onboarded successfully with admin '{$request->admin_email}'.");
    }

    /**
     * Show edit company form
     */
    public function edit(Company $company)
    {
        $primaryContractor = $company->contractors()->first();
        return view('super-admin.companies.edit', compact('company', 'primaryContractor'));
    }

    /**
     * Update company settings
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'code'           => ['nullable', 'string', 'max:50', 'unique:companies,code,' . $company->id],
            'contact_person' => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],
            'status'         => ['required', 'in:active,suspended,trial'],
            'max_workers'    => ['required', 'integer', 'min:1'],
            'max_meters'     => ['required', 'integer', 'min:100'],
        ]);

        $company->update($request->only([
            'name', 'code', 'contact_person', 'email', 'phone', 'address', 'status', 'max_workers', 'max_meters'
        ]));

        return redirect()->route('super-admin.companies.index')
            ->with('success', "Company '{$company->name}' updated successfully.");
    }

    /**
     * Fast toggle company status: active <-> suspended
     */
    public function toggleStatus(Company $company)
    {
        $newStatus = $company->status === 'active' ? 'suspended' : 'active';
        $company->update(['status' => $newStatus]);

        return back()->with('success', "Company '{$company->name}' status changed to " . ucfirst($newStatus) . ".");
    }

    /**
     * Impersonate a contractor to view and assist their operations
     */
    public function impersonate(Company $company)
    {
        $contractor = $company->contractors()->first();

        if (!$contractor) {
            return back()->with('error', "No contractor admin found for '{$company->name}'.");
        }

        // Store current Super Admin ID in session
        session(['impersonator_id' => Auth::id()]);

        // Login as the contractor
        Auth::login($contractor);

        return redirect()->route('dashboard')
            ->with('success', "Now logged in as {$contractor->name} ({$company->name}).");
    }

    /**
     * Soft delete company
     */
    public function destroy(Company $company)
    {
        $name = $company->name;
        $company->delete();

        return redirect()->route('super-admin.companies.index')
            ->with('success', "Company '{$name}' has been archived.");
    }
}
