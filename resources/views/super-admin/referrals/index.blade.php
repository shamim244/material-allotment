<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Platform Referrals Ledger') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Track contractor agency invitations, referral reward status, and wallet crediting.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2 text-right">
                    <span class="text-xs uppercase font-semibold text-indigo-500 block">Total Rewards Paid</span>
                    <span class="text-xl font-bold text-emerald-600">₹{{ number_format($stats['total_paid_reward'], 2) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- METRICS CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <span class="text-xs text-gray-400 block">Total Referrals Registered</span>
                <span class="text-2xl font-bold text-gray-900 mt-1 block">{{ $stats['total_referrals'] }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <span class="text-xs text-gray-400 block">Rewards Credited</span>
                <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $stats['rewarded'] }}</span>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <span class="text-xs text-gray-400 block">Pending Subscription Activation</span>
                <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $stats['pending'] }}</span>
            </div>
        </div>

        {{-- REFERRALS TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[11px] border-b border-gray-200">
                            <th class="py-3.5 px-4 font-semibold">Referrer Agency</th>
                            <th class="py-3.5 px-4 font-semibold">Referred Agency</th>
                            <th class="py-3.5 px-4 font-semibold">Date Registered</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Reward Amount</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold">Rewarded At</th>
                            <th class="py-3.5 px-4 font-semibold">Transaction Ref</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($referrals as $ref)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900">{{ $ref->referrerCompany->name ?? 'Deleted' }}</div>
                                    <div class="font-mono text-gray-400 text-[11px]">{{ $ref->referrerCompany->code ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900">{{ $ref->referredCompany->name ?? 'Deleted' }}</div>
                                    <div class="font-mono text-indigo-600 text-[11px]">{{ $ref->referredCompany->code ?? '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                    {{ $ref->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-600 whitespace-nowrap">
                                    ₹{{ number_format($ref->reward_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $ref->status === 'rewarded' ? 'bg-emerald-100 text-emerald-700' : ($ref->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ ucfirst($ref->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                    {{ $ref->rewarded_at ? $ref->rewarded_at->format('M d, Y h:i A') : 'Pending' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-gray-400 text-[11px] whitespace-nowrap">
                                    {{ $ref->walletTransaction?->reference_id ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No referral records logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($referrals->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $referrals->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
