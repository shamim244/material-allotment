<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Accounting Report') }}
            </h2>
            <div class="text-xs text-gray-400">Updated {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ── Overview Stats ── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 px-4 sm:px-0">
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-indigo-500">
                    <div class="text-2xl font-bold text-indigo-600">{{ $totalMeters }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Meters</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-green-500">
                    <div class="text-2xl font-bold text-green-600">{{ $inStock }}</div>
                    <div class="text-xs text-gray-500 mt-1">In Stock</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-blue-500">
                    <div class="text-2xl font-bold text-blue-600">{{ $withWorkers }}</div>
                    <div class="text-xs text-gray-500 mt-1">With Workers</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-emerald-500">
                    <div class="text-2xl font-bold text-emerald-600">{{ $totalInstalled }}</div>
                    <div class="text-xs text-gray-500 mt-1">Installed</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-amber-500">
                    <div class="text-2xl font-bold text-amber-600">{{ $totalReturned }}</div>
                    <div class="text-xs text-gray-500 mt-1">Returned</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-red-500">
                    <div class="text-2xl font-bold text-red-600">{{ $totalAssigned }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Assignments</div>
                </div>
            </div>

            {{-- ── Installation Type + MI Status ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4 sm:px-0">
                {{-- Installation Type Breakdown --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Installation Types</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">🆕 NSC</span>
                                <span class="text-sm font-bold text-indigo-700">{{ $nscInstalls }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-indigo-500 h-2.5 rounded-full transition-all" style="width: {{ ($nscInstalls + $replacementInstalls) > 0 ? round($nscInstalls / ($nscInstalls + $replacementInstalls) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">🔄 Replacement</span>
                                <span class="text-sm font-bold text-orange-700">{{ $replacementInstalls }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-orange-500 h-2.5 rounded-full transition-all" style="width: {{ ($nscInstalls + $replacementInstalls) > 0 ? round($replacementInstalls / ($nscInstalls + $replacementInstalls) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center text-xs text-gray-400">
                        Total: {{ $nscInstalls + $replacementInstalls }} installations
                    </div>
                </div>

                {{-- MI Status Breakdown --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">MI Status Overview</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-2 px-3 py-2 bg-yellow-50 rounded-lg">
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                            <span class="text-sm text-gray-700">Pending</span>
                            <span class="ml-auto text-sm font-bold text-yellow-700">{{ $miPending }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-green-50 rounded-lg">
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                            <span class="text-sm text-gray-700">Complete</span>
                            <span class="ml-auto text-sm font-bold text-green-700">{{ $miComplete }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-red-50 rounded-lg">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                            <span class="text-sm text-gray-700">Issue</span>
                            <span class="ml-auto text-sm font-bold text-red-700">{{ $miIssue }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 rounded-lg">
                            <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
                            <span class="text-sm text-gray-700">JNF</span>
                            <span class="ml-auto text-sm font-bold text-purple-700">{{ $miJnf }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Worker-wise Material Summary ── --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl px-4 sm:px-0">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Worker-wise Material Summary</h3>
                        <span class="text-xs text-gray-400">{{ $workerSummary->count() }} workers</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Held</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assigned</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Installed</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issues</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Returned</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">🆕 NSC</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">🔄 RPL</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pending</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($workerSummary as $ws)
                                    @php
                                        $returned = $returnedPerWorker[$ws->id] ?? 0;
                                        $pending = $ws->held_count - $ws->installed_count - ($ws->issue_count ?? 0);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('workers.show', $ws->id) }}" class="text-sm font-medium text-indigo-600 hover:underline">{{ $ws->name }}</a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center font-semibold text-blue-600">{{ $ws->held_count }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $ws->assigned_count }}</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="text-emerald-600 font-semibold">{{ $ws->installed_count }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($ws->issue_count > 0)
                                                <span class="text-red-600 font-semibold">{{ $ws->issue_count }}</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-amber-600 font-medium">{{ $returned }}</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($ws->nsc_count > 0)
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 font-medium">{{ $ws->nsc_count }}</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($ws->replacement_count > 0)
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 font-medium">{{ $ws->replacement_count }}</span>
                                            @else
                                                <span class="text-gray-400">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if($pending > 0)
                                                <span class="font-bold text-red-600">{{ $pending }}</span>
                                            @else
                                                <span class="text-green-600 font-medium">✓ 0</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">No workers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            {{-- Totals Row --}}
                            @if($workerSummary->isNotEmpty())
                            <tfoot class="bg-gray-50 font-semibold">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">TOTAL</td>
                                    <td class="px-4 py-3 text-sm text-center text-blue-700">{{ $workerSummary->sum('held_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $workerSummary->sum('assigned_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-emerald-700">{{ $workerSummary->sum('installed_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-red-700">{{ $workerSummary->sum('issue_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-amber-700">{{ $returnedPerWorker->sum() }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-indigo-700">{{ $workerSummary->sum('nsc_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-orange-700">{{ $workerSummary->sum('replacement_count') }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-red-700">—</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Ledger Entries ── --}}
            @if($ledger->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl px-4 sm:px-0">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Ledger Entries</h3>
                        <span class="text-xs text-gray-400">Last {{ $ledger->count() }} entries</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($ledger as $entry)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($entry->type === 'debit')
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-medium">↗ Debit</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-medium">↙ Credit</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $entry->description }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold {{ $entry->type === 'debit' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $entry->type === 'debit' ? '-' : '+' }}{{ number_format($entry->amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
