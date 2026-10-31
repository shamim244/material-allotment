<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Agency / Company Name -->
        <div class="mt-4">
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="company_name" :value="__('Agency / Company Name (Contractors)')" />
                <span class="text-xs text-slate-400">Optional for staff</span>
            </div>
            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" placeholder="e.g. Apex Power Infrastructure" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Referral Code -->
        <div class="mt-4">
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="referral_code" :value="__('Referral Code (Optional)')" />
                @if(!empty($referralCode ?? request('ref')))
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                        Referral Applied
                    </span>
                @endif
            </div>
            <x-text-input id="referral_code" class="block mt-1 w-full font-mono text-sm" type="text" name="referral_code" :value="old('referral_code', $referralCode ?? request('ref'))" placeholder="e.g. NCC-REF-XXX" />
            <x-input-error :messages="$errors->get('referral_code')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Work Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 gap-4">
            <a class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors" href="{{ route('login') }}">
                {{ __('Already registered? Log in') }}
            </a>

            <x-primary-button class="w-full sm:w-auto">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
