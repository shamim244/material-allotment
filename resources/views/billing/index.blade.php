<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Billing & Agency Wallet') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage your agency subscription tier, wallet credits, and referral earnings.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $company->hasActiveSubscription() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    <span class="w-2 h-2 rounded-full {{ $company->hasActiveSubscription() ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    {{ $company->hasActiveSubscription() ? 'Active Subscription' : 'Subscription Inactive / Expired' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- SUBSCRIPTION LIFECYCLE BANNER: 7-DAY WARNING OR EXPIRED --}}
        @if ($company->isSubscriptionExpired())
            <div class="p-5 rounded-2xl bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-rose-500/10 border border-rose-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-xl font-bold">
                        ⚠️
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-rose-900">Your Subscription has Expired</h4>
                        <p class="text-sm text-rose-700 mt-0.5">
                            Please renew your agency plan below or utilize your wallet balance to reactivate platform throughput and prevent meter assignment interruptions.
                        </p>
                    </div>
                </div>
                <a href="#plans-section" class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow transition shrink-0">
                    Renew Now
                </a>
            </div>
        @elseif ($company->isSubscriptionExpiringSoon(7))
            @php $daysLeft = $company->daysRemainingOnSubscription(); @endphp
            <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-400/10 to-amber-500/10 border border-amber-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-xl font-bold">
                        ⏳
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-amber-900">Subscription Expiring in {{ $daysLeft }} {{ Str::plural('Day', $daysLeft) }}</h4>
                        <p class="text-sm text-amber-700 mt-0.5">
                            Your active plan ends on {{ $currentSubscription?->ends_at->format('M d, Y') }}. Renewing now extends your expiration seamlessly without losing any days.
                        </p>
                    </div>
                </div>
                <a href="#plans-section" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow transition shrink-0">
                    Extend Subscription
                </a>
            </div>
        @elseif (!$company->hasActiveSubscription())
            <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-indigo-500/10 border border-indigo-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 text-xl font-bold">
                        🚀
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-indigo-900">Activate Your Agency Subscription</h4>
                        <p class="text-sm text-indigo-700 mt-0.5">
                            Welcome to Material Allotment! Select an agency tier below to activate your account and start registering meter lots and technician assignments.
                        </p>
                    </div>
                </div>
                <a href="#plans-section" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition shrink-0">
                    Choose Plan &darr;
                </a>
            </div>
        @endif

        {{-- TOP ROW: ACTIVE PLAN & AGENCY WALLET CARDS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- CARD 1: ACTIVE SUBSCRIPTION CARD (2 COLUMNS) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative overflow-hidden flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                            📦
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Current Plan</span>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ $currentSubscription ? $currentSubscription->plan_name : 'No Active Plan' }}
                                </h3>
                                @if($currentSubscription?->is_custom_deal)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Custom Deal</span>
                                @elseif($currentSubscription?->plan?->badge)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ $currentSubscription->plan->badge }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($currentSubscription)
                        <div class="text-left sm:text-right">
                            <span class="text-xs text-gray-500 block">Valid Until</span>
                            <span class="text-sm font-semibold text-gray-800">
                                {{ $currentSubscription->ends_at->format('M d, Y') }}
                                <span class="text-xs font-normal text-gray-500">({{ $currentSubscription->daysRemaining() }} days left)</span>
                            </span>
                        </div>
                    @endif
                </div>

                {{-- QUOTA USAGE METERS & WORKERS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                    {{-- Meters Quota --}}
                    @php
                        $maxMeters = $company->max_meters > 0 ? $company->max_meters : 1;
                        $meterPercent = min(100, round(($usedMeters / $maxMeters) * 100));
                    @endphp
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700 flex items-center gap-1.5">
                                ⚡ Meter Capacity
                            </span>
                            <span class="text-xs font-semibold text-gray-500">
                                {{ number_format($usedMeters) }} / {{ number_format($company->max_meters) }} ({{ $meterPercent }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $meterPercent > 90 ? 'bg-rose-500' : ($meterPercent > 70 ? 'bg-amber-500' : 'bg-indigo-600') }}"
                                 style="width: {{ $meterPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            Total meter inventory registered across all lots.
                        </p>
                    </div>

                    {{-- Workers Quota --}}
                    @php
                        $maxWorkers = $company->max_workers > 0 ? $company->max_workers : 1;
                        $workerPercent = min(100, round(($usedWorkers / $maxWorkers) * 100));
                    @endphp
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700 flex items-center gap-1.5">
                                👷 Field Workers
                            </span>
                            <span class="text-xs font-semibold text-gray-500">
                                {{ number_format($usedWorkers) }} / {{ number_format($company->max_workers) }} ({{ $workerPercent }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $workerPercent > 90 ? 'bg-rose-500' : ($workerPercent > 70 ? 'bg-amber-500' : 'bg-emerald-600') }}"
                                 style="width: {{ $workerPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            Active field technician logins allocated to your company.
                        </p>
                    </div>
                </div>

                {{-- Action button --}}
                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs text-gray-500">
                        Need higher meter throughput or more field workers? Upgrade your plan below anytime.
                    </span>
                    <a href="#plans-section" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        View Upgrade Options &rarr;
                    </a>
                </div>
            </div>

            {{-- CARD 2: AGENCY WALLET CARD (1 COLUMN) --}}
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-2xl shadow-lg p-6 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>

                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Agency Wallet Balance</span>
                        <span class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-sm">💳</span>
                    </div>

                    <div class="mt-4">
                        <div class="text-3xl font-extrabold tracking-tight text-white flex items-baseline gap-1">
                            <span>₹</span>{{ number_format($company->wallet_balance, 2) }}
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Available credit to discount plan subscriptions & renewals.
                        </p>
                    </div>
                </div>

                {{-- Quick referral share --}}
                <div class="mt-6 pt-4 border-t border-white/10" x-data="{ copied: false }">
                    <span class="text-xs text-slate-400 block mb-1">Your Referral Code</span>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-white/10 border border-white/10 rounded-lg px-3 py-1.5 text-xs font-mono font-semibold tracking-wider text-indigo-200 truncate">
                            {{ $company->referral_code ?? 'GEN-REFERRAL' }}
                        </div>
                        <button
                            @click="navigator.clipboard.writeText('{{ $company->referral_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            type="button"
                            class="px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-medium text-white transition shrink-0 flex items-center gap-1">
                            <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                        </button>
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1.5 block">
                        🎁 Earn ₹500 wallet credit for every referred agency.
                    </span>
                </div>
            </div>
        </div>

        {{-- PLANS SELECTION SECTION WITH MULTI-CYCLE TOGGLE --}}
        <div id="plans-section" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8"
             x-data="{
                activeCycle: '1y',
                useWallet: true,
                walletBalance: {{ (float) $company->wallet_balance }},
                plans: {{ json_encode($plans) }},
                getCyclePrice(plan) {
                    return plan['price_' + this.activeCycle] || plan.price_1m;
                },
                getDiscount(plan) {
                    const price = this.getCyclePrice(plan);
                    if (!this.useWallet || this.walletBalance <= 0) return 0;
                    return Math.min(this.walletBalance, price);
                },
                getNetPayable(plan) {
                    const price = this.getCyclePrice(plan);
                    const discount = this.getDiscount(plan);
                    return Math.max(0, price - discount);
                }
             }">

            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                    Transparent Multi-Cycle Pricing
                </span>
                <h3 class="text-2xl font-bold text-gray-900 mt-3">
                    Select Your Agency Subscription Tier
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Choose the billing duration that fits your project timeline. Save up to 30% with annual plans.
                </p>

                {{-- MULTI-CYCLE SWITCH TOGGLE [ 1 Mo | 3 Mo | 6 Mo | 1 Yr ] --}}
                <div class="inline-flex p-1 bg-gray-100 rounded-xl mt-6 border border-gray-200">
                    <button
                        type="button"
                        @click="activeCycle = '1m'"
                        :class="activeCycle === '1m' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                        class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5">
                        <span>1 Month</span>
                    </button>
                    <button
                        type="button"
                        @click="activeCycle = '3m'"
                        :class="activeCycle === '3m' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                        class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5">
                        <span>3 Months</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-indigo-50 text-indigo-600 font-bold">10% Off</span>
                    </button>
                    <button
                        type="button"
                        @click="activeCycle = '6m'"
                        :class="activeCycle === '6m' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                        class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5">
                        <span>6 Months</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-indigo-50 text-indigo-600 font-bold">16% Off</span>
                    </button>
                    <button
                        type="button"
                        @click="activeCycle = '1y'"
                        :class="activeCycle === '1y' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                        class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5">
                        <span>1 Year</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-emerald-50 text-emerald-600 font-bold">Best Value (~30% Off)</span>
                    </button>
                </div>

                {{-- WALLET APPLIED TOGGLE --}}
                @if ($company->wallet_balance > 0)
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-700 bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-full">
                            <input type="checkbox" switch role="switch" x-model="useWallet" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer accent-emerald-600">
                            <span>Use available wallet balance (₹{{ number_format($company->wallet_balance, 2) }}) for instant discount</span>
                        </label>
                    </div>
                @endif
            </div>

            {{-- PLAN CARDS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    @php
                        $isCurrent = $currentSubscription && $currentSubscription->plan_id === $plan->id && $currentSubscription->isActive();
                    @endphp
                    <div class="rounded-2xl border transition-all duration-200 flex flex-col justify-between p-6 relative
                                {{ $plan->is_featured ? 'border-indigo-500 shadow-md ring-2 ring-indigo-500/20 bg-indigo-50/10' : 'border-gray-200 hover:border-gray-300 hover:shadow-sm bg-white' }}">

                        @if($plan->badge)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase shadow-sm {{ $plan->is_featured ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-white' }}">
                                    {{ $plan->badge }}
                                </span>
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between">
                                <h4 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h4>
                                @if($isCurrent)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Active</span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 mt-2 min-h-[32px]">
                                {{ $plan->description }}
                            </p>

                            {{-- Price display dynamically driven by Alpine --}}
                            <div class="my-6 p-4 rounded-xl bg-gray-50 border border-gray-100 text-center">
                                <div class="text-3xl font-extrabold text-gray-900 flex items-baseline justify-center gap-1">
                                    <span>₹</span>
                                    <span x-text="getNetPayable(plans.find(p => p.id === {{ $plan->id }})).toLocaleString('en-IN')"></span>
                                    <span class="text-xs font-normal text-gray-500" x-text="'/ ' + (activeCycle === '1m' ? 'month' : (activeCycle === '3m' ? '3 mos' : (activeCycle === '6m' ? '6 mos' : 'year')))"></span>
                                </div>

                                {{-- Strikethrough original price if wallet discount applied --}}
                                <div x-show="useWallet && walletBalance > 0 && getDiscount(plans.find(p => p.id === {{ $plan->id }})) > 0" class="text-xs text-gray-400 mt-1">
                                    <span class="line-through">₹<span x-text="getCyclePrice(plans.find(p => p.id === {{ $plan->id }})).toLocaleString('en-IN')"></span></span>
                                    <span class="text-emerald-600 font-semibold ml-1">
                                        (-₹<span x-text="getDiscount(plans.find(p => p.id === {{ $plan->id }})).toLocaleString('en-IN')"></span> wallet)
                                    </span>
                                </div>
                            </div>

                            {{-- Quotas --}}
                            <div class="space-y-2 py-3 border-t border-b border-gray-100 my-4 text-xs">
                                <div class="flex items-center justify-between text-gray-700">
                                    <span class="font-medium text-gray-500">Meters Limit</span>
                                    <span class="font-bold text-gray-900">{{ number_format($plan->max_meters) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-gray-700">
                                    <span class="font-medium text-gray-500">Field Workers</span>
                                    <span class="font-bold text-gray-900">{{ number_format($plan->max_workers) }}</span>
                                </div>
                            </div>

                            {{-- Features checklist --}}
                            <ul class="space-y-2 text-xs text-gray-600 my-4">
                                @if(is_array($plan->features))
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        {{-- Subscribe Form --}}
                        <form method="POST" action="{{ route('billing.subscribe') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="cycle" :value="activeCycle">
                            <input type="hidden" name="use_wallet" :value="useWallet ? 1 : 0">

                            <button
                                type="submit"
                                onclick="return confirm('Confirm subscription to {{ $plan->name }}? Your wallet balance will be automatically applied to discount the amount.')"
                                class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition duration-150 shadow-sm
                                       {{ $isCurrent
                                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                            : ($plan->is_featured ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-900 hover:bg-black text-white') }}">
                                {{ $isCurrent ? 'Extend Active Plan' : 'Subscribe to ' . $plan->name }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BOTTOM ROW: WALLET TRANSACTION PASSBOOK & REFERRAL ENGINE --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- PASSBOOK / LEDGER (2 COLUMNS) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h4 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span>📖</span> Agency Wallet Passbook
                        </h4>
                        <p class="text-xs text-gray-400 mt-0.5">Immutable audit ledger of all wallet credits and debits</p>
                    </div>
                    <span class="text-xs text-gray-500 font-semibold">
                        Balance: ₹{{ number_format($company->wallet_balance, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider border-b border-gray-100 text-[11px]">
                                <th class="pb-3 font-semibold">Date & Time</th>
                                <th class="pb-3 font-semibold">Description</th>
                                <th class="pb-3 font-semibold">Ref ID</th>
                                <th class="pb-3 font-semibold text-right">Amount</th>
                                <th class="pb-3 font-semibold text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-3 text-gray-500 whitespace-nowrap">
                                        {{ $tx->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="py-3 text-gray-800 font-medium">
                                        {{ $tx->reason }}
                                    </td>
                                    <td class="py-3 font-mono text-gray-400 text-[11px]">
                                        {{ $tx->reference_id ?? '—' }}
                                    </td>
                                    <td class="py-3 text-right font-semibold whitespace-nowrap {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="py-3 text-right text-gray-800 font-bold whitespace-nowrap">
                                        ₹{{ number_format($tx->balance_after, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400">
                                        No transactions recorded in passbook yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

            {{-- REFER & EARN CARD (1 COLUMN) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                        <span class="text-xl">🤝</span>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Refer & Earn Credits</h4>
                            <p class="text-xs text-gray-400">Invite fellow agencies to the platform</p>
                        </div>
                    </div>

                    <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100/60 my-4 text-xs space-y-2">
                        <p class="text-indigo-900 font-medium leading-relaxed">
                            Share your referral code. When a contractor registers and activates a subscription plan, you automatically receive <strong class="text-indigo-700">₹500</strong> directly in your agency wallet!
                        </p>
                    </div>

                    {{-- Referral stats --}}
                    <div class="grid grid-cols-2 gap-3 my-4">
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                            <span class="text-xs text-gray-400 block">Agencies Referred</span>
                            <span class="text-lg font-bold text-gray-900">{{ $referrals->count() }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                            <span class="text-xs text-gray-400 block">Rewards Earned</span>
                            <span class="text-lg font-bold text-emerald-600">₹{{ number_format($totalReferralRewards, 2) }}</span>
                        </div>
                    </div>

                    {{-- Referred agencies list --}}
                    <div class="mt-4">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-2">Referred Agencies</span>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @forelse($referrals as $ref)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-100 text-xs">
                                    <div>
                                        <span class="font-semibold text-gray-800 block truncate max-w-[140px]">{{ $ref->referredCompany->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $ref->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $ref->status === 'rewarded' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $ref->status === 'rewarded' ? '+₹500 Credited' : 'Pending Activation' }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-3 text-center text-xs text-gray-400 bg-gray-50 rounded-xl">
                                    No referrals yet. Share your code to earn wallet balance!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100" x-data="{ linkCopied: false }">
                    <span class="text-xs text-gray-400 block mb-1">Share Referral Link</span>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText('{{ url('/register') }}?ref={{ $company->referral_code }}'); linkCopied = true; setTimeout(() => linkCopied = false, 2000)"
                        class="w-full py-2 px-3 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span x-text="linkCopied ? 'Link Copied to Clipboard!' : 'Copy Invite Link'"></span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
