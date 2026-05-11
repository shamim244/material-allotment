<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ═══ TABS ═══ --}}
            <div class="mb-6 px-4 sm:px-0">
                <div class="flex flex-wrap gap-1 bg-white rounded-lg shadow-sm p-1">
                    @foreach([
                        'meters'        => '📊 All Meters',
                        'workers'       => '👷 Worker Performance',
                        'mi'            => '⚡ MI Report',
                        'installations' => '📋 Installation Details',
                    ] as $key => $label)
                        <a href="{{ route('reports.index', ['tab' => $key]) }}"
                           class="flex-1 text-center px-4 py-2.5 text-sm font-medium rounded-md transition
                                  {{ $tab === $key ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ═══ FILTERS ═══ --}}
            @if($tab !== 'workers')
            <div class="mb-4 px-4 sm:px-0">
                <form method="GET" action="{{ route('reports.index') }}" class="bg-white rounded-lg shadow-sm p-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="flex flex-wrap gap-3 items-end">

                        {{-- Search --}}
                        <div class="flex-1 min-w-[140px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Search Meter #</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Meter number..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Lot — Multi-select with completion % --}}
                        <div class="min-w-[180px]" x-data="{ open: false }">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Lot</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500 bg-white text-left flex justify-between items-center">
                                    <span class="truncate">
                                        @if(!empty($filters['lot_id']))
                                            {{ count((array)$filters['lot_id']) }} lot(s)
                                        @else
                                            All Lots
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-md shadow-lg p-2 space-y-1 max-h-60 overflow-y-auto">
                                    @foreach($lots as $lot)
                                        <label class="flex items-center gap-2 px-2 py-1.5 hover:bg-gray-50 rounded cursor-pointer text-sm {{ $lot->is_complete ? 'opacity-60' : '' }}">
                                            <input type="checkbox" name="lot_id[]" value="{{ $lot->id }}"
                                                {{ in_array($lot->id, (array)($filters['lot_id'] ?? [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="flex-1 truncate">{{ $lot->lot_number }}</span>
                                            <span class="text-xs font-medium px-1.5 py-0.5 rounded-full {{ $lot->is_complete ? 'bg-green-100 text-green-700' : ($lot->completion_pct > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500') }}">
                                                {{ $lot->is_complete ? '✅' : $lot->completion_pct . '%' }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Worker — Multi-select --}}
                        <div class="min-w-[160px]" x-data="{ open: false }">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Worker</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500 bg-white text-left flex justify-between items-center">
                                    <span class="truncate">
                                        @if(!empty($filters['worker_id']))
                                            {{ count((array)$filters['worker_id']) }} worker(s)
                                        @else
                                            All Workers
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg p-2 space-y-1 max-h-60 overflow-y-auto">
                                    @foreach($workers as $w)
                                        <label class="flex items-center gap-2 px-2 py-1 hover:bg-gray-50 rounded cursor-pointer text-sm">
                                            <input type="checkbox" name="worker_id[]" value="{{ $w->id }}"
                                                {{ in_array($w->id, (array)($filters['worker_id'] ?? [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ $w->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Status (meters tab) — Multi-select checkboxes --}}
                        @if($tab === 'meters')
                        <div class="min-w-[180px]" x-data="{ open: false }">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500 bg-white text-left flex justify-between items-center">
                                    <span class="truncate">
                                        @if(!empty($filters['status']))
                                            {{ count((array)$filters['status']) }} selected
                                        @else
                                            All
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg p-2 space-y-1">
                                    @foreach(['stock','assigned','installed','issue','returned','mi_complete','mi_issue','return_pending'] as $s)
                                        <label class="flex items-center gap-2 px-2 py-1 hover:bg-gray-50 rounded cursor-pointer text-sm">
                                            <input type="checkbox" name="status[]" value="{{ $s }}"
                                                {{ in_array($s, (array)($filters['status'] ?? [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ ucfirst(str_replace('_',' ',$s)) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- MI Status — Multi-select checkboxes --}}
                        @if(in_array($tab, ['meters', 'mi']))
                        <div class="min-w-[160px]" x-data="{ open: false }">
                            <label class="block text-xs font-medium text-gray-500 mb-1">MI Status</label>
                            <div class="relative">
                                <button type="button" @click="open = !open" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500 bg-white text-left flex justify-between items-center">
                                    <span class="truncate">
                                        @if(!empty($filters['mi_status']))
                                            {{ count((array)$filters['mi_status']) }} selected
                                        @else
                                            All
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg p-2 space-y-1">
                                    @foreach(['pending','issue','complete','job_not_found'] as $s)
                                        <label class="flex items-center gap-2 px-2 py-1 hover:bg-gray-50 rounded cursor-pointer text-sm">
                                            <input type="checkbox" name="mi_status[]" value="{{ $s }}"
                                                {{ in_array($s, (array)($filters['mi_status'] ?? [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ $s === 'job_not_found' ? 'Job Not Found' : ucfirst($s) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Date Range --}}
                        <div class="min-w-[130px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="min-w-[130px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Install Type --}}
                        <div class="min-w-[120px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select name="install_type" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All</option>
                                <option value="nsc" {{ ($filters['install_type'] ?? '') === 'nsc' ? 'selected' : '' }}>🆕 NSC</option>
                                <option value="replacement" {{ ($filters['install_type'] ?? '') === 'replacement' ? 'selected' : '' }}>🔄 Replacement</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                            🔍 Filter
                        </button>
                        <a href="{{ route('reports.index', ['tab' => $tab]) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 transition">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
            @endif

            {{-- ═══ EXPORT BUTTON ═══ --}}
            <div class="mb-4 px-4 sm:px-0 flex justify-end">
                @php
                    $exportRoutes = [
                        'meters'        => 'reports.export.meters',
                        'workers'       => 'reports.export.workers',
                        'mi'            => 'reports.export.mi',
                        'installations' => 'reports.export.installations',
                    ];
                @endphp
                <a href="{{ route($exportRoutes[$tab], request()->except('tab', 'page')) }}"
                   class="inline-flex items-center bg-emerald-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Excel (CSV)
                </a>
            </div>

            {{-- ═══ TAB CONTENT ═══ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    {{-- ─── ALL METERS TAB ─── --}}
                    @if($tab === 'meters')
                        {{-- Summary Cards --}}
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                            @foreach([
                                ['Total', $summary['total'], 'text-gray-700', 'bg-gray-50'],
                                ['Stock', $summary['stock'], 'text-green-700', 'bg-green-50'],
                                ['Assigned', $summary['assigned'], 'text-blue-700', 'bg-blue-50'],
                                ['Installed', $summary['installed'], 'text-emerald-700', 'bg-emerald-50'],
                                ['Issues', $summary['issue'], 'text-red-700', 'bg-red-50'],
                            ] as [$label, $count, $textColor, $bgColor])
                                <div class="{{ $bgColor }} rounded-lg p-3 text-center">
                                    <div class="text-xs uppercase text-gray-500">{{ $label }}</div>
                                    <div class="text-xl font-bold {{ $textColor }}">{{ $count }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Meter #</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Group</th>
                                        <th class="px-4 py-3">Worker</th>
                                        <th class="px-4 py-3">CA #</th>
                                        <th class="px-4 py-3">Old Meter #</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">MI Status</th>
                                        <th class="px-4 py-3">Updated</th>
                                        <th class="px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($meters as $meter)
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
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">
                                                <a href="{{ route('stock.show', $meter) }}" class="text-indigo-600 hover:underline">{{ $meter->meter_number }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($meter->installationDetail)
                                                    @if($meter->installationDetail->install_type === 'replacement')
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">🔄 RPL</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">🆕 NSC</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">
                                                @if($meter->group)
                                                    <a href="{{ route('stock.groups.show', $meter->group) }}" class="text-indigo-600 hover:underline">{{ $meter->group->name }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                @if($meter->currentWorker)
                                                    <a href="{{ route('workers.show', $meter->currentWorker) }}" class="text-indigo-600 hover:underline">{{ $meter->currentWorker->name }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $meter->installationDetail?->ca_number ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $meter->installationDetail?->old_meter_number ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <form method="POST" action="{{ route('reports.meter.status', $meter) }}" class="inline-flex items-center gap-1" x-data>
                                                    @csrf @method('PATCH')
                                                    <select name="status" @change="$el.closest('form').submit()"
                                                        class="text-xs rounded border-gray-200 py-1 px-2 {{ $statusColors[$meter->status] ?? '' }} cursor-pointer">
                                                        @foreach(['stock','assigned','installed','issue','returned'] as $s)
                                                            <option value="{{ $s }}" {{ $meter->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($meter->activation)
                                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $miColors[$meter->activation->mi_status] ?? 'bg-gray-100' }}">
                                                        {{ $meter->activation->mi_status === 'job_not_found' ? 'JNF' : ucfirst($meter->activation->mi_status) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-400">{{ $meter->updated_at->format('d M, H:i') }}</td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('stock.show', $meter) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">No meters found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $meters->withQueryString()->links() }}</div>

                    {{-- ─── WORKER PERFORMANCE TAB ─── --}}
                    @elseif($tab === 'workers')
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Worker</th>
                                        <th class="px-4 py-3 text-center">Assigned</th>
                                        <th class="px-4 py-3 text-center">Held</th>
                                        <th class="px-4 py-3 text-center">Installed</th>
                                        <th class="px-4 py-3 text-center">Issues</th>
                                        <th class="px-4 py-3 text-center">🆕 NSC</th>
                                        <th class="px-4 py-3 text-center">🔄 RPL</th>
                                        <th class="px-4 py-3 text-center">MI Pending</th>
                                        <th class="px-4 py-3 text-center">MI Complete</th>
                                        <th class="px-4 py-3 text-center">MI Issue</th>
                                        <th class="px-4 py-3 text-center">JNF</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($workerStats as $w)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">
                                                <a href="{{ route('workers.show', $w) }}" class="text-indigo-600 hover:underline">{{ $w->name }}</a>
                                            </td>
                                            <td class="px-4 py-3 text-center">{{ $w->assignments_count }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700 font-semibold">{{ $w->held_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700 font-semibold">{{ $w->installed_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs {{ $w->issue_count > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }} font-semibold">{{ $w->issue_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700 font-semibold">{{ $w->nsc_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-700 font-semibold">{{ $w->rpl_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">{{ $w->mi_pending }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">{{ $w->mi_complete }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">{{ $w->mi_issue }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs {{ $w->job_not_found > 0 ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500' }}">{{ $w->job_not_found }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="11" class="px-4 py-8 text-center text-gray-400">No workers found.</td></tr>
                                    @endforelse
                                </tbody>
                                {{-- Totals row --}}
                                @if($workerStats->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-gray-100 font-bold text-sm">
                                        <td class="px-4 py-3">Total</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('assignments_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('held_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('installed_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('issue_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('nsc_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('rpl_count') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('mi_pending') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('mi_complete') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('mi_issue') }}</td>
                                        <td class="px-4 py-3 text-center">{{ $workerStats->sum('job_not_found') }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                    {{-- ─── MI REPORT TAB ─── --}}
                    @elseif($tab === 'mi')
                        {{-- MI Summary Cards --}}
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                            @foreach([
                                ['Total', $miSummary['total'], 'text-gray-700', 'bg-gray-50'],
                                ['Pending', $miSummary['pending'], 'text-yellow-700', 'bg-yellow-50'],
                                ['Complete', $miSummary['complete'], 'text-green-700', 'bg-green-50'],
                                ['Issue', $miSummary['issue'], 'text-red-700', 'bg-red-50'],
                                ['Job Not Found', $miSummary['job_not_found'], 'text-purple-700', 'bg-purple-50'],
                            ] as [$label, $count, $textColor, $bgColor])
                                <div class="{{ $bgColor }} rounded-lg p-3 text-center">
                                    <div class="text-xs uppercase text-gray-500">{{ $label }}</div>
                                    <div class="text-xl font-bold {{ $textColor }}">{{ $count }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Meter #</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Worker</th>
                                        <th class="px-4 py-3">MI Status</th>
                                        <th class="px-4 py-3">Request # / CA #</th>
                                        <th class="px-4 py-3">Mobile</th>
                                        <th class="px-4 py-3">Remark</th>
                                        <th class="px-4 py-3">Updated</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($activations as $a)
                                        @php
                                            $miColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'issue' => 'bg-red-100 text-red-700',
                                                'complete' => 'bg-green-100 text-green-700',
                                                'job_not_found' => 'bg-purple-100 text-purple-700',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">
                                                <a href="{{ route('mi.show', $a) }}" class="text-indigo-600 hover:underline">{{ $a->meter->meter_number }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($a->meter->installationDetail?->install_type === 'replacement')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">🔄 RPL</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">🆕 NSC</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('workers.show', $a->worker) }}" class="text-indigo-600 hover:underline">{{ $a->worker->name }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                <form method="POST" action="{{ route('reports.mi.status', $a) }}" class="inline" x-data>
                                                    @csrf @method('PATCH')
                                                    <select name="mi_status" @change="$el.closest('form').submit()"
                                                        class="text-xs rounded border-gray-200 py-1 px-2 {{ $miColors[$a->mi_status] ?? '' }} cursor-pointer">
                                                        @foreach(['pending','issue','job_not_found','complete'] as $s)
                                                            <option value="{{ $s }}" {{ $a->mi_status === $s ? 'selected' : '' }}>
                                                                {{ $s === 'job_not_found' ? 'Job Not Found' : ucfirst($s) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">
                                                @if($a->meter->installationDetail?->install_type === 'replacement')
                                                    {{ $a->meter->installationDetail?->ca_number ?? '—' }}
                                                @else
                                                    {{ $a->meter->installationDetail?->request_number ?? '—' }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">{{ $a->meter->installationDetail?->mobile_number ?? '—' }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500 max-w-[150px] truncate">{{ $a->remark ?? '—' }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-400">{{ $a->updated_at->format('d M, H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No MI records found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $activations->links() }}</div>

                    {{-- ─── INSTALLATION DETAILS TAB ─── --}}
                    @elseif($tab === 'installations')
                        {{-- Installation Summary Cards --}}
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-xs uppercase text-gray-500">Total</div>
                                <div class="text-xl font-bold text-gray-700">{{ $installSummary['total'] }}</div>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                <div class="text-xs uppercase text-gray-500">🆕 NSC</div>
                                <div class="text-xl font-bold text-indigo-700">{{ $installSummary['nsc'] }}</div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3 text-center">
                                <div class="text-xs uppercase text-gray-500">🔄 Replacement</div>
                                <div class="text-xl font-bold text-orange-700">{{ $installSummary['replacement'] }}</div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Meter #</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Group</th>
                                        <th class="px-4 py-3">Worker</th>
                                        <th class="px-4 py-3">Request # / CA #</th>
                                        <th class="px-4 py-3">Mobile</th>
                                        <th class="px-4 py-3">Lat / Lng</th>
                                        <th class="px-4 py-3">Remark</th>
                                        <th class="px-4 py-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($installations as $d)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">
                                                <a href="{{ route('stock.show', $d->meter) }}" class="text-indigo-600 hover:underline">{{ $d->meter->meter_number }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($d->install_type === 'replacement')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">🔄 RPL</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">🆕 NSC</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">
                                                @if($d->meter->group)
                                                    <a href="{{ route('stock.groups.show', $d->meter->group) }}" class="text-indigo-600 hover:underline">{{ $d->meter->group->name }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('workers.show', $d->worker) }}" class="text-indigo-600 hover:underline">{{ $d->worker->name }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($d->install_type === 'replacement')
                                                    {{ $d->ca_number ?? '—' }}
                                                @else
                                                    {{ $d->request_number ?? '—' }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $d->mobile_number ?? '—' }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                @if($d->latitude && $d->longitude)
                                                    <a href="https://maps.google.com/?q={{ $d->latitude }},{{ $d->longitude }}" target="_blank"
                                                       class="text-indigo-600 hover:underline">📍 {{ number_format($d->latitude, 4) }}, {{ number_format($d->longitude, 4) }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-500 max-w-[150px] truncate">{{ $d->remark ?? '—' }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-400">{{ $d->created_at->format('d M Y, H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">No installation details found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $installations->links() }}</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
