<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🔎</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Custom Report') }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
            @endif

            {{-- ═══ SAVED TEMPLATES ═══ --}}
            @if($savedReports->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mx-4 sm:mx-0" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" class="w-full p-4 flex items-center justify-between hover:bg-gray-50 transition">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">💾</span>
                        <span class="font-semibold text-gray-800">Saved Templates ({{ $savedReports->count() }})</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="expanded" x-cloak x-transition class="border-t border-gray-100">
                    <div class="divide-y divide-gray-50">
                        @foreach($savedReports as $report)
                            <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 group">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm text-gray-800">{{ $report->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ count(array_filter(explode(',', $report->meter_numbers))) }} meters
                                        • Saved {{ $report->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 opacity-60 group-hover:opacity-100 transition">
                                    <a href="{{ route('reports.custom', ['template' => $report->id]) }}"
                                       class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                                        📂 Load
                                    </a>
                                    <form method="POST" action="{{ route('reports.custom.template.delete', $report) }}" onsubmit="return confirm('Delete this template?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 bg-red-100 text-red-600 text-xs font-medium rounded-lg hover:bg-red-200 transition">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ═══ INPUT FORM ═══ --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">📋 Paste Meter Numbers</h3>
                    <p class="text-sm text-gray-500 mt-1">Enter multiple meter numbers — comma, space, or new line separated. Use saved templates to quickly reload daily reports.</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('reports.custom.generate') }}" id="generateForm">
                        @csrf
                        <textarea name="meters" rows="4" id="meterInput"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm p-3 border focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                            placeholder="1004112&#10;1004113&#10;1004114&#10;or: 1004112, 1004113, 1004114">{{ old('meters', $input) }}</textarea>
                        @error('meters')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-wrap gap-2 mt-3">
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                🔍 Generate Report
                            </button>
                            @if($generated)
                                <a href="{{ route('reports.custom') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">Clear</a>
                            @endif
                        </div>
                    </form>

                    {{-- Save as Template --}}
                    <div class="mt-3 pt-3 border-t border-gray-100" x-data="{ showSave: false }">
                        <button @click="showSave = !showSave" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            💾 Save as Template
                        </button>
                        <div x-show="showSave" x-cloak x-transition class="mt-2">
                            <form method="POST" action="{{ route('reports.custom.save') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="meters" id="saveMetersInput">
                                <input type="text" name="name" placeholder="Template name (e.g. March Batch 1)" required
                                    class="flex-1 text-sm rounded-lg border-gray-300 py-2 px-3 border focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" onclick="document.getElementById('saveMetersInput').value = document.getElementById('meterInput').value"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                    Save
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($generated)
                {{-- ═══ OVERVIEW STATS ═══ --}}
                <div class="grid grid-cols-3 gap-3 px-4 sm:px-0">
                    <div class="bg-gray-50 rounded-xl shadow-sm p-4 text-center border-l-4 border-gray-400">
                        <div class="text-2xl font-bold text-gray-700">{{ ($stats['total'] ?? 0) + count($missing) }}</div>
                        <div class="text-xs text-gray-500 mt-1 font-medium">Total Entered</div>
                    </div>
                    <div class="bg-green-50 rounded-xl shadow-sm p-4 text-center border-l-4 border-green-500">
                        <div class="text-2xl font-bold text-green-700">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-xs text-green-600 mt-1 font-medium">✅ Found</div>
                    </div>
                    <div class="bg-red-50 rounded-xl shadow-sm p-4 text-center border-l-4 border-red-500">
                        <div class="text-2xl font-bold text-red-700">{{ count($missing) }}</div>
                        <div class="text-xs text-red-600 mt-1 font-medium">❌ Not Found</div>
                    </div>
                </div>

                {{-- ═══ STATUS BREAKDOWN ═══ --}}
                @if(($stats['total'] ?? 0) > 0)
                <div x-data="{ show: false }" class="mx-4 sm:mx-0">
                    <button @click="show = !show" class="w-full bg-white rounded-xl shadow-sm p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <span class="text-sm font-semibold text-gray-700">📊 Detailed Breakdown</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': show }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="show" x-cloak x-transition class="bg-white rounded-b-xl shadow-sm -mt-2 p-4 pt-6 space-y-4">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase mb-2">Meter Status</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach([
                                    ['Stock', $stats['stock'] ?? 0, 'bg-green-100 text-green-700'],
                                    ['Assigned', $stats['assigned'] ?? 0, 'bg-blue-100 text-blue-700'],
                                    ['Installed', $stats['installed'] ?? 0, 'bg-emerald-100 text-emerald-700'],
                                    ['Issue', $stats['issue'] ?? 0, 'bg-red-100 text-red-700'],
                                    ['Returned', $stats['returned'] ?? 0, 'bg-gray-100 text-gray-700'],
                                    ['MI Complete', $stats['mi_complete'] ?? 0, 'bg-teal-100 text-teal-700'],
                                    ['MI Issue', $stats['mi_issue'] ?? 0, 'bg-orange-100 text-orange-700'],
                                    ['Return Pending', $stats['return_pending'] ?? 0, 'bg-amber-100 text-amber-700'],
                                ] as [$label, $count, $color])
                                    @if($count > 0)
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $color }}">{{ $label }}: {{ $count }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase mb-2">Installation Type</div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700">🆕 NSC: {{ $stats['nsc'] ?? 0 }}</span>
                                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-orange-100 text-orange-700">🔄 RPL: {{ $stats['replacement'] ?? 0 }}</span>
                            </div>
                        </div>
                        @if(($stats['mi_pending'] ?? 0) + ($stats['mi_complete_act'] ?? 0) + ($stats['mi_issue_act'] ?? 0) + ($stats['mi_jnf'] ?? 0) > 0)
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase mb-2">MI Status</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach([
                                    ['Pending', $stats['mi_pending'] ?? 0, 'bg-yellow-100 text-yellow-700'],
                                    ['Complete', $stats['mi_complete_act'] ?? 0, 'bg-green-100 text-green-700'],
                                    ['Issue', $stats['mi_issue_act'] ?? 0, 'bg-red-100 text-red-700'],
                                    ['Job Not Found', $stats['mi_jnf'] ?? 0, 'bg-purple-100 text-purple-700'],
                                ] as [$label, $count, $color])
                                    @if($count > 0)
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $color }}">{{ $label }}: {{ $count }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- ═══ FOUND METERS ═══ --}}
                @if($meters->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mx-4 sm:mx-0"
                     x-data="{
                        selectAll: false,
                        selected: [],
                        filter: 'all',
                        toggleAll() {
                            if (this.selectAll) {
                                this.selected = Array.from(document.querySelectorAll('.meter-checkbox:not([style*=&quot;display: none&quot;])')).filter(el => el.closest('.meter-row').style.display !== 'none').map(el => el.value);
                            } else {
                                this.selected = [];
                            }
                        },
                        get selectedCount() { return this.selected.length; }
                     }">

                    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-800">✅ Found Meters ({{ $meters->count() }})</h3>
                        <div class="flex flex-wrap gap-2">
                            <select x-model="filter" @change="
                                document.querySelectorAll('.meter-row').forEach(row => {
                                    row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
                                });
                                selectAll = false; selected = [];
                            " class="text-xs rounded-lg border-gray-300 py-1.5 px-3">
                                <option value="all">All Status</option>
                                @foreach(['stock','assigned','installed','issue','returned','mi_complete','mi_issue','return_pending'] as $s)
                                    <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                @endforeach
                            </select>

                            <form method="POST" action="{{ route('reports.custom.export') }}" class="inline">
                                @csrf
                                <input type="hidden" name="meters" value="{{ $input }}">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    CSV
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Bulk Action Bar --}}
                    <div x-show="selectedCount > 0" x-cloak class="px-4 py-3 bg-indigo-50 border-b border-indigo-100 flex flex-col sm:flex-row sm:items-center gap-3">
                        <span class="text-sm font-medium text-indigo-700" x-text="selectedCount + ' meter(s) selected'"></span>
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('reports.custom.bulk') }}" class="inline-flex items-center gap-1" x-ref="bulkStatusForm">
                                @csrf
                                <input type="hidden" name="action" value="status">
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="meter_ids[]" :value="id">
                                </template>
                                <select name="status" @change="if($el.value) $refs.bulkStatusForm.submit()" class="text-xs rounded-lg border-gray-300 py-1.5 px-2">
                                    <option value="">Change Status →</option>
                                    @foreach(['stock','assigned','installed','issue','returned'] as $s)
                                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form method="POST" action="{{ route('reports.custom.bulk') }}" class="inline-flex items-center gap-1" x-ref="bulkLotForm">
                                @csrf
                                <input type="hidden" name="action" value="lot">
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="meter_ids[]" :value="id">
                                </template>
                                <select name="lot_id" @change="if($el.value) $refs.bulkLotForm.submit()" class="text-xs rounded-lg border-gray-300 py-1.5 px-2">
                                    <option value="">Assign Lot →</option>
                                    @foreach($lots as $lot)
                                        <option value="{{ $lot->id }}">{{ $lot->lot_number }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-3 py-3"><input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-gray-300 text-indigo-600"></th>
                                    <th class="px-3 py-3">Meter #</th>
                                    <th class="px-3 py-3">Type</th>
                                    <th class="px-3 py-3">Group</th>
                                    <th class="px-3 py-3">Lot</th>
                                    <th class="px-3 py-3">Worker</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3">MI</th>
                                    <th class="px-3 py-3">CA #</th>
                                    <th class="px-3 py-3">Old Meter</th>
                                    <th class="px-3 py-3">Mobile</th>
                                    <th class="px-3 py-3">Updated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $statusColors = [
                                        'stock' => 'bg-green-100 text-green-700',
                                        'assigned' => 'bg-blue-100 text-blue-700',
                                        'installed' => 'bg-emerald-100 text-emerald-700',
                                        'issue' => 'bg-red-100 text-red-700',
                                        'returned' => 'bg-gray-100 text-gray-700',
                                        'mi_complete' => 'bg-teal-100 text-teal-700',
                                        'mi_issue' => 'bg-orange-100 text-orange-700',
                                        'return_pending' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $miColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'issue' => 'bg-red-100 text-red-700',
                                        'complete' => 'bg-green-100 text-green-700',
                                        'job_not_found' => 'bg-purple-100 text-purple-700',
                                    ];
                                @endphp
                                @foreach($meters as $meter)
                                    <tr class="hover:bg-gray-50 meter-row" data-status="{{ $meter->status }}">
                                        <td class="px-3 py-2.5"><input type="checkbox" value="{{ $meter->id }}" x-model="selected" class="meter-checkbox rounded border-gray-300 text-indigo-600"></td>
                                        <td class="px-3 py-2.5 font-medium">
                                            <a href="{{ route('stock.show', $meter) }}" class="text-indigo-600 hover:underline">{{ $meter->meter_number }}</a>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if($meter->installationDetail)
                                                @if($meter->installationDetail->install_type === 'replacement')
                                                    <span class="px-1.5 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">RPL</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">NSC</span>
                                                @endif
                                            @else <span class="text-gray-300 text-xs">—</span> @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500">
                                            @if($meter->group)
                                                <a href="{{ route('stock.groups.show', $meter->group) }}" class="text-indigo-600 hover:underline">{{ $meter->group->name }}</a>
                                            @else — @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500">{{ $meter->lot?->lot_number ?? '—' }}</td>
                                        <td class="px-3 py-2.5 text-xs">
                                            @if($meter->currentWorker)
                                                <a href="{{ route('workers.show', $meter->currentWorker) }}" class="text-indigo-600 hover:underline">{{ $meter->currentWorker->name }}</a>
                                            @else — @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100' }}">
                                                {{ ucfirst(str_replace('_', ' ', $meter->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if($meter->activation)
                                                <span class="px-2 py-0.5 text-xs rounded-full {{ $miColors[$meter->activation->mi_status] ?? 'bg-gray-100' }}">
                                                    {{ $meter->activation->mi_status === 'job_not_found' ? 'JNF' : ucfirst($meter->activation->mi_status) }}
                                                </span>
                                            @else <span class="text-gray-300 text-xs">—</span> @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500">{{ $meter->installationDetail?->ca_number ?? '—' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500">{{ $meter->installationDetail?->old_meter_number ?? '—' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-500">{{ $meter->installationDetail?->mobile_number ?? '—' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-400">{{ $meter->updated_at->format('d M, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="sm:hidden divide-y divide-gray-100">
                        @foreach($meters as $meter)
                            <div class="p-4 hover:bg-gray-50 meter-row" data-status="{{ $meter->status }}">
                                <div class="flex items-start gap-2">
                                    <input type="checkbox" value="{{ $meter->id }}" x-model="selected" class="meter-checkbox rounded border-gray-300 text-indigo-600 mt-1">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('stock.show', $meter) }}" class="font-bold text-indigo-600 hover:underline">{{ $meter->meter_number }}</a>
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100' }}">
                                                {{ ucfirst(str_replace('_', ' ', $meter->status)) }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 mt-1">
                                            @if($meter->installationDetail)
                                                <span>{{ $meter->installationDetail->install_type === 'replacement' ? '🔄 RPL' : '🆕 NSC' }}</span>
                                            @endif
                                            @if($meter->currentWorker) <span>👷 {{ $meter->currentWorker->name }}</span> @endif
                                            @if($meter->group) <span>📦 {{ $meter->group->name }}</span> @endif
                                            @if($meter->lot) <span>🏷️ {{ $meter->lot->lot_number }}</span> @endif
                                            @if($meter->installationDetail?->ca_number) <span>CA: {{ $meter->installationDetail->ca_number }}</span> @endif
                                            @if($meter->installationDetail?->mobile_number) <span>📱 {{ $meter->installationDetail->mobile_number }}</span> @endif
                                            @if($meter->activation)
                                                <span class="px-1.5 py-0.5 rounded-full {{ $miColors[$meter->activation->mi_status] ?? '' }}">
                                                    MI: {{ $meter->activation->mi_status === 'job_not_found' ? 'JNF' : ucfirst($meter->activation->mi_status) }}
                                                </span>
                                            @endif
                                            <span class="text-gray-400">{{ $meter->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ═══ MISSING METERS ═══ --}}
                @if(count($missing) > 0)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mx-4 sm:mx-0 border-l-4 border-red-500" x-data="{ copied: false }">
                    <div class="p-4 border-b border-gray-100 bg-red-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">❌ Not In System ({{ count($missing) }})</h3>
                            <p class="text-sm text-red-600 mt-0.5">These meter numbers were not found in the database.</p>
                        </div>
                        <button @click="
                            navigator.clipboard.writeText('{{ implode(', ', $missing) }}');
                            copied = true; setTimeout(() => copied = false, 2000);
                        " class="inline-flex items-center px-3 py-1.5 bg-white border border-red-200 text-red-700 text-xs font-medium rounded-lg hover:bg-red-50 transition">
                            <span x-show="!copied">📋 Copy All</span>
                            <span x-show="copied" x-cloak class="text-green-600">✅ Copied!</span>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($missing as $num)
                                <span class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 text-sm font-mono font-medium rounded-lg border border-red-200">{{ $num }}</span>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('reports.custom.import') }}" x-data="{ confirm: false }">
                            @csrf
                            <input type="hidden" name="missing_meters" value="{{ implode(',', $missing) }}">
                            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                                <div class="sm:w-48">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Assign to Lot (optional)</label>
                                    <select name="lot_id" class="w-full text-sm rounded-lg border-gray-300 py-2 px-3">
                                        <option value="">No Lot</option>
                                        @foreach($lots as $lot)
                                            <option value="{{ $lot->id }}">{{ $lot->lot_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="!confirm">
                                    <button type="button" @click="confirm = true"
                                        class="inline-flex items-center px-4 py-2.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Import {{ count($missing) }} as Stock
                                    </button>
                                </div>
                                <div x-show="confirm" x-cloak class="flex items-center gap-2">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">✅ Confirm</button>
                                    <button type="button" @click="confirm = false" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if($meters->isEmpty() && count($missing) === 0)
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center mx-4 sm:mx-0">
                        <div class="text-5xl mb-3">🤷</div>
                        <div class="text-lg font-semibold text-gray-700">No Results</div>
                        <div class="text-sm text-gray-500 mt-1">No valid meter numbers were found in your input.</div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
