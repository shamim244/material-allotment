<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Wallet Passbook: ') }} {{ $company->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Complete immutable transaction ledger for agency {{ $company->code }}.
                </p>
            </div>
            <a href="{{ route('super-admin.wallets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Wallets
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- SUMMARY CARD --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-semibold text-gray-400">Current Balance</span>
                <div class="text-3xl font-extrabold text-emerald-600 mt-1">
                    ₹{{ number_format($company->wallet_balance, 2) }}
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    Referral Code: <span class="font-mono text-gray-800">{{ $company->referral_code ?? 'None' }}</span>
                </div>
            </div>

            <div class="text-xs text-gray-500 space-y-1">
                <div>Contact: <strong class="text-gray-800">{{ $company->contact_person }}</strong></div>
                <div>Email: <strong class="text-gray-800">{{ $company->email }}</strong></div>
                <div>Status: <span class="font-semibold text-emerald-600">{{ ucfirst($company->status) }}</span></div>
            </div>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-900">Transaction History Ledger</h4>
                <span class="text-xs text-gray-400">Immutable record</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[11px] border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold">Timestamp</th>
                            <th class="py-3 px-4 font-semibold">Type</th>
                            <th class="py-3 px-4 font-semibold">Description</th>
                            <th class="py-3 px-4 font-semibold">Reference ID</th>
                            <th class="py-3 px-4 font-semibold text-right">Amount</th>
                            <th class="py-3 px-4 font-semibold text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3 px-4 text-gray-500 whitespace-nowrap">
                                    {{ $tx->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tx->type === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ strtoupper($tx->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-medium text-gray-800">
                                    {{ $tx->reason }}
                                </td>
                                <td class="py-3 px-4 font-mono text-gray-400 text-[11px]">
                                    {{ $tx->reference_id ?? '—' }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold whitespace-nowrap {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                    ₹{{ number_format($tx->balance_after, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    No transactions recorded for this agency yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
