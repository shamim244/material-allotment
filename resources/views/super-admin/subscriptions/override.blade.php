<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Custom Deal & Plan Override: ') }} {{ $company->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Grant one-off custom deals (e.g., 7-day trial, 45-day deal, custom quotas & prices) without cluttering the master plans table.
                </p>
            </div>
            <a href="{{ route('super-admin.companies.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Companies
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

            {{-- CURRENT STATUS SUMMARY --}}
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase font-semibold text-gray-400">Current Status</span>
                    <div class="font-bold text-gray-900 text-sm mt-0.5">
                        {{ $currentSubscription ? $currentSubscription->plan_name : 'No Active Subscription' }}
                    </div>
                    @if($currentSubscription)
                        <div class="text-xs text-gray-500 mt-0.5">
                            Expires on {{ $currentSubscription->ends_at->format('M d, Y') }} ({{ $currentSubscription->daysRemaining() }} days left)
                        </div>
                    @endif
                </div>

                <div class="text-xs text-gray-500">
                    <div>Agency Code: <strong class="text-gray-800">{{ $company->code }}</strong></div>
                    <div>Referral Code: <strong class="text-gray-800">{{ $company->referral_code ?? 'None' }}</strong></div>
                    <div>Wallet Balance: <strong class="text-emerald-600">₹{{ number_format($company->wallet_balance, 2) }}</strong></div>
                </div>
            </div>

            <form method="POST" action="{{ route('super-admin.companies.override-subscription.store', $company) }}" class="space-y-6">
                @csrf

                {{-- TEMPLATE SELECTION (OPTIONAL) --}}
                <div>
                    <x-input-label for="plan_id" :value="__('Base Plan Template (Optional)')" />
                    <select id="plan_id" name="plan_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Fully Custom Deal (No Base Template) --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ number_format($plan->max_meters) }} meters, {{ number_format($plan->max_workers) }} workers)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Select an existing tier to inherit quotas or leave blank for completely ad-hoc deals.</p>
                </div>

                {{-- DEAL DURATION & CUSTOM PRICE --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <x-input-label for="duration_days" :value="__('Deal Duration (Days)')" />
                        <x-text-input id="duration_days" class="block mt-1 w-full text-sm font-semibold" type="number" name="duration_days" :value="old('duration_days', 30)" required min="1" max="3650" />
                        <p class="text-xs text-gray-400 mt-1">e.g. 7 for free trial, 45 for mid-cycle deal, 180 for special seasonal deal.</p>
                        <x-input-error :messages="$errors->get('duration_days')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="price" :value="__('Custom Deal Price (₹)')" />
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-xs">₹</span>
                            <x-text-input id="price" class="block pl-7 w-full text-sm font-semibold" type="number" step="0.01" name="price" :value="old('price', '0.00')" required />
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Set to 0.00 for complimentary trial / platform gift deal.</p>
                        <x-input-error :messages="$errors->get('price')" class="mt-1" />
                    </div>
                </div>

                {{-- CUSTOM LIMITS OVERRIDE --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <x-input-label for="max_meters" :value="__('Max Meters Quota Override')" />
                        <x-text-input id="max_meters" class="block mt-1 w-full text-sm" type="number" name="max_meters" :value="old('max_meters', $company->max_meters ?: 50000)" required min="100" />
                        <x-input-error :messages="$errors->get('max_meters')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="max_workers" :value="__('Max Workers Quota Override')" />
                        <x-text-input id="max_workers" class="block mt-1 w-full text-sm" type="number" name="max_workers" :value="old('max_workers', $company->max_workers ?: 25)" required min="1" />
                        <x-input-error :messages="$errors->get('max_workers')" class="mt-1" />
                    </div>
                </div>

                {{-- DEAL JUSTIFICATION NOTES --}}
                <div class="pt-4 border-t border-gray-100">
                    <x-input-label for="notes" :value="__('Reason & Deal Justification (Audit Note)')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. VIP onboarding promotional 45-day extension approved by Platform Director." required>{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('super-admin.companies.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Assign Custom Deal
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
