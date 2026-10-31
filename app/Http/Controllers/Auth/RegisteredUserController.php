<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $referralCode = $request->query('ref');

        return view('auth.register', compact('referralCode'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'company_name' => ['nullable', 'string', 'max:255'],
            'referral_code' => ['nullable', 'string', 'max:50', 'exists:companies,referral_code'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->filled('company_name')) {
            $referrer = null;
            if ($request->filled('referral_code')) {
                $referrer = Company::where('referral_code', trim($request->referral_code))->first();
            }

            $company = Company::create([
                'name' => $request->company_name,
                'code' => 'NCC-'.str_pad(Company::count() + 1, 3, '0', STR_PAD_LEFT),
                'referral_code' => Company::generateUniqueReferralCode('NCC-REF'),
                'referred_by_id' => $referrer?->id,
                'contact_person' => $request->name,
                'email' => $request->email,
                'status' => 'active',
                'wallet_balance' => 0.00,
                'max_workers' => 15,
                'max_meters' => 25000,
            ]);

            if ($referrer && $referrer->id !== $company->id) {
                Referral::create([
                    'referrer_company_id' => $referrer->id,
                    'referred_company_id' => $company->id,
                    'reward_amount' => 500.00,
                    'status' => 'pending',
                ]);
            }

            $user = User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'plain_password' => $request->password,
                'role' => 'contractor',
                'is_active' => true,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('billing.index')
                ->with('success', "Welcome {$request->name}! Your agency '{$company->name}' is ready. Select a subscription plan below to get started.");
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false,
        ]);

        event(new Registered($user));

        // Don't auto-login — account is pending admin approval
        return redirect()->route('login')
            ->with('status', 'Registration successful! Your account is pending admin approval. You will be able to login once approved.');
    }
}
