<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $query = User::withTrashed();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at')->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->whereNull('deleted_at')->where('is_active', false);
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        $stats = [
            'total' => User::withTrashed()->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'deleted' => User::onlyTrashed()->count(),
            'contractors' => User::where('role', 'contractor')->count(),
            'workers' => User::where('role', 'worker')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:contractor,worker'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => $request->role,
            'is_active' => true,
        ]);

        try {
            if (\App\Models\Setting::where('key', 'email_welcome_enabled')->value('value') !== 'false') {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user, $request->password));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome email: '.$e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$request->name}' created successfully.");
    }

    public function edit(User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:contractor,worker'],
            'is_active' => ['required', 'in:0,1,true,false'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->role = $request->role;
        $user->is_active = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->plain_password = $request->password;
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    public function toggleActive(User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        // Don't allow deactivating yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User '{$user->name}' has been {$status}.");
    }

    public function impersonate(User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        // Don't impersonate yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Store the admin's ID so we can return later
        session()->put('impersonator_id', Auth::id());
        session()->put('impersonator_name', Auth::user()->name);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', "You are now logged in as {$user->name}.");
    }

    public function stopImpersonating()
    {
        $adminId = session()->get('impersonator_id');

        if (! $adminId) {
            return redirect()->route('dashboard');
        }

        $admin = User::withoutGlobalScope('company')->findOrFail($adminId);

        session()->forget('impersonator_id');
        session()->forget('impersonator_name');

        Auth::login($admin);

        if ($admin->role === 'super_admin') {
            return redirect()->route('super-admin.companies.index')
                ->with('success', 'You have returned to your Super Admin account.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'You have returned to your admin account.');
    }

    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // Soft delete

        return back()->with('success', "User '{$user->name}' has been deleted (data preserved).");
    }

    public function restore($id)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', "User '{$user->name}' has been restored.");
    }
}
