<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    {{ __('Agency Wallets & Platform Credits') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage contractor wallet balances, promotional gifts, manual adjustments, and audit passbooks.
                </p>
            </div>
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2 text-right">
                <span class="text-xs uppercase font-semibold text-indigo-500 block">Total Platform Liability</span>
                <span class="text-xl font-bold text-indigo-900">₹{{ number_format($totalPlatformWalletLiability, 2) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ adjustModalOpen: false, selectedCompany: null }">

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- SEARCH BAR --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('super-admin.wallets.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company by name, agency code, or referral code..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('super-admin.wallets.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- WALLET BALANCES TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[11px] border-b border-gray-200">
                            <th class="py-3.5 px-4 font-semibold">Agency</th>
                            <th class="py-3.5 px-4 font-semibold">Codes</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Wallet Balance</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Transactions</th>
                            <th class="py-3.5 px-4 font-semibold text-center">Referrals Made</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                            <th class="py-3.5 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($companies as $company)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $company->name }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $company->contact_person }} &bull; {{ $company->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-mono text-gray-700 text-xs font-semibold">{{ $company->code }}</div>
                                    <div class="font-mono text-indigo-600 text-[11px] mt-0.5">Ref: {{ $company->referral_code ?? 'None' }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-extrabold {{ $company->wallet_balance > 0 ? 'text-emerald-600' : 'text-gray-500' }}">
                                        ₹{{ number_format($company->wallet_balance, 2) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-center font-medium text-gray-600">
                                    {{ $company->wallet_transactions_count }} entries
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-center font-medium text-gray-600">
                                    {{ $company->referrals_made_count }} agencies
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $company->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-right space-x-2">
                                    <button
                                        type="button"
                                        @click="selectedCompany = {{ json_encode(['id' => $company->id, 'name' => $company->name, 'balance' => $company->wallet_balance]) }}; adjustModalOpen = true"
                                        class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                        Gift / Adjust
                                    </button>
                                    <a href="{{ route('super-admin.wallets.history', $company) }}" class="text-xs text-gray-600 hover:text-gray-900 font-medium">
                                        Passbook &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    No agencies found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($companies->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $companies->links() }}
                </div>
            @endif
        </div>

        {{-- RECENT TRANSACTION FEED --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h4 class="text-sm font-bold text-gray-900 mb-3">Recent Platform-Wide Wallet Activity</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 uppercase tracking-wider text-[11px] border-b border-gray-100">
                            <th class="pb-2.5 font-semibold">Time</th>
                            <th class="pb-2.5 font-semibold">Company</th>
                            <th class="pb-2.5 font-semibold">Reason</th>
                            <th class="pb-2.5 font-semibold">Ref ID</th>
                            <th class="pb-2.5 font-semibold text-right">Amount</th>
                            <th class="pb-2.5 font-semibold text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-2.5 text-gray-400 whitespace-nowrap">{{ $tx->created_at->format('M d, H:i') }}</td>
                                <td class="py-2.5 font-medium text-gray-900">{{ $tx->company->name ?? '—' }}</td>
                                <td class="py-2.5 text-gray-700">{{ $tx->reason }}</td>
                                <td class="py-2.5 font-mono text-[11px] text-gray-400">{{ $tx->reference_id ?? '—' }}</td>
                                <td class="py-2.5 text-right font-bold {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="py-2.5 text-right font-semibold text-gray-800">₹{{ number_format($tx->balance_after, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ADJUSTMENT MODAL --}}
        <div
            x-show="adjustModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div
                @click.away="adjustModalOpen = false"
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-xs relative">

                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">
                        Gift / Adjust Agency Wallet
                    </h3>
                    <button type="button" @click="adjustModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form :action="'/super-admin/wallets/' + (selectedCompany ? selectedCompany.id : 0) + '/adjust'" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-gray-400 text-[11px] block">Agency</span>
                        <div class="font-bold text-gray-900 text-sm" x-text="selectedCompany ? selectedCompany.name : ''"></div>
                        <div class="text-gray-500 mt-0.5">Current Balance: <strong class="text-emerald-600" x-text="selectedCompany ? '₹' + Number(selectedCompany.balance).toFixed(2) : ''"></strong></div>
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('Transaction Type')" />
                        <select id="type" name="type" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="credit">Credit (+) — Promotional Gift / Bonus</option>
                            <option value="debit">Debit (-) — Manual Correction / Clawback</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="amount" :value="__('Amount (₹)')" />
                        <x-text-input id="amount" class="block mt-1 w-full text-xs font-semibold" type="number" step="0.01" min="0.01" name="amount" placeholder="e.g. 500.00" required />
                    </div>

                    <div>
                        <x-input-label for="reason" :value="__('Reason / Audit Justification')" />
                        <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm text-xs focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Special festive promo credit approved by Support Lead" required></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button type="button" @click="adjustModalOpen = false" class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            Apply Wallet Adjustment
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
