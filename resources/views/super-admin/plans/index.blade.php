<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Subscription Plans Master') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage tier definitions, quotas, and multi-cycle billing prices across the platform.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('super-admin.plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Plan Tier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- STATUS FILTER TABS --}}
        <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
            <a href="{{ route('super-admin.plans.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900' }}">
                All Tiers ({{ $plans->count() }})
            </a>
            <a href="{{ route('super-admin.plans.index', ['status' => 'active']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'active' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900' }}">
                Active Public
            </a>
            <a href="{{ route('super-admin.plans.index', ['status' => 'promo']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'promo' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900' }}">
                Promo / Trial
            </a>
            <a href="{{ route('super-admin.plans.index', ['status' => 'archived']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'archived' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:text-gray-900' }}">
                Archived
            </a>
        </div>

        {{-- PLANS TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[11px] border-b border-gray-200">
                            <th class="py-3.5 px-4 font-semibold">Tier / Badge</th>
                            <th class="py-3.5 px-4 font-semibold">Quotas</th>
                            <th class="py-3.5 px-4 font-semibold">1 Month</th>
                            <th class="py-3.5 px-4 font-semibold">3 Months</th>
                            <th class="py-3.5 px-4 font-semibold">6 Months</th>
                            <th class="py-3.5 px-4 font-semibold">1 Year</th>
                            <th class="py-3.5 px-4 font-semibold">Subscribers</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-gray-900 text-sm">{{ $plan->name }}</div>
                                        @if($plan->badge)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                {{ $plan->badge }}
                                            </span>
                                        @endif
                                        @if($plan->is_featured)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Featured</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-0.5 max-w-xs truncate">{{ $plan->description }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-800">{{ number_format($plan->max_meters) }} meters</div>
                                    <div class="text-gray-400 text-[11px]">{{ number_format($plan->max_workers) }} workers</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-700">
                                    ₹{{ number_format($plan->price_1m, 2) }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-700">
                                    ₹{{ number_format($plan->price_3m, 2) }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-700">
                                    ₹{{ number_format($plan->price_6m, 2) }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-gray-900">
                                    ₹{{ number_format($plan->price_1y, 2) }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 font-semibold">
                                    {{ $plan->subscriptions_count }} active
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $plan->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($plan->status === 'promo' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('super-admin.plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('super-admin.plans.toggle', $plan) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs text-gray-500 hover:text-gray-800 font-medium">
                                            {{ $plan->status === 'archived' ? 'Activate' : 'Archive' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-gray-400">
                                    No subscription plans configured yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
