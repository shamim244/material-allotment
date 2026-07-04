<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="p-6 max-w-[1400px] mx-auto space-y-6">

            {{-- ── WELCOME BANNER ──────────────────────────────── --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-[#1e40af] via-[#1d4ed8] to-[#3b82f6] rounded-2xl px-6 py-5 flex items-center justify-between shadow-lg">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSA2MCAwIEwgMCAwIDAgNjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC4zIiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZykiLz48L3N2Zz4=')] opacity-30 pointer-events-none"></div>
                <div class="relative z-10">
                    <p class="text-blue-200 text-sm font-medium">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
                    <h1 class="text-white text-2xl font-bold mt-0.5">{{ Auth::user()->name }}</h1>
                    <p class="text-blue-200 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="relative z-10 hidden sm:grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold text-white">{{ $stats['total_meters'] }}</div>
                        <div class="text-xs text-blue-200 mt-0.5">Total Meters</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold text-white">{{ $stats['total_workers'] }}</div>
                        <div class="text-xs text-blue-200 mt-0.5">Workers</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3">
                        <div class="text-2xl font-bold text-white">{{ $stats['pending_returns'] }}</div>
                        <div class="text-xs text-blue-200 mt-0.5">Pending Returns</div>
                    </div>
                </div>
            </div>

            {{-- ── PRIMARY KPI GRID ─────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
                @php
                    $kpis = [
                        ['label'=>'In Stock',       'val'=>$stats['in_stock'],       'color'=>'text-emerald-600', 'bg'=>'bg-emerald-50', 'ring'=>'ring-emerald-200', 'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'href'=>route('stock.index')],
                        ['label'=>'Assigned',       'val'=>$stats['assigned'],       'color'=>'text-blue-600',   'bg'=>'bg-blue-50',   'ring'=>'ring-blue-200',   'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'href'=>route('assignments.create')],
                        ['label'=>'Installed',      'val'=>$stats['installed'],      'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50', 'ring'=>'ring-indigo-200', 'icon'=>'M5 13l4 4L19 7', 'href'=>route('reports.index')],
                        ['label'=>'Issues',         'val'=>$stats['issues'],         'color'=>'text-red-600',    'bg'=>'bg-red-50',    'ring'=>'ring-red-200',    'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'href'=>route('action-required')],
                        ['label'=>'Rtn Pending',    'val'=>$stats['return_pending'], 'color'=>'text-orange-600', 'bg'=>'bg-orange-50', 'ring'=>'ring-orange-200', 'icon'=>'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'href'=>route('returns.index')],
                        ['label'=>'Returned',       'val'=>$stats['returned'],       'color'=>'text-gray-600',   'bg'=>'bg-gray-50',   'ring'=>'ring-gray-200',   'icon'=>'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3', 'href'=>route('reports.index')],
                    ];
                @endphp
                @foreach($kpis as $k)
                    <a href="{{ $k['href'] }}"
                       class="group {{ $k['bg'] }} ring-1 {{ $k['ring'] }} rounded-2xl p-4 flex flex-col gap-2 hover:shadow-md transition-all hover:-translate-y-0.5">
                        <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 {{ $k['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold {{ $k['color'] }}">{{ number_format($k['val']) }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 font-medium">{{ $k['label'] }}</div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- ── MI STATUS ROW ────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $miCards = [
                        ['label'=>'MI Pending',       'val'=>$stats['mi_pending'],       'bg'=>'bg-amber-50',   'text'=>'text-amber-700',  'ring'=>'ring-amber-200',  'status'=>'pending'],
                        ['label'=>'MI Issues',         'val'=>$stats['mi_issue'],         'bg'=>'bg-red-50',    'text'=>'text-red-700',    'ring'=>'ring-red-200',    'status'=>'issue'],
                        ['label'=>'MI Complete',       'val'=>$stats['mi_complete'],      'bg'=>'bg-green-50',  'text'=>'text-green-700',  'ring'=>'ring-green-200',  'status'=>'complete'],
                        ['label'=>'Job Not Found',     'val'=>$stats['mi_job_not_found'], 'bg'=>'bg-slate-50',  'text'=>'text-slate-600',  'ring'=>'ring-slate-200',  'status'=>'job_not_found'],
                    ];
                @endphp
                @foreach($miCards as $m)
                    <a href="{{ route('mi.index', ['status' => $m['status']]) }}"
                       class="{{ $m['bg'] }} ring-1 {{ $m['ring'] }} rounded-2xl p-4 text-center hover:shadow-md transition-all hover:-translate-y-0.5">
                        <div class="text-3xl font-bold {{ $m['text'] }}">{{ number_format($m['val']) }}</div>
                        <div class="text-xs text-gray-500 font-medium mt-1">{{ $m['label'] }}</div>
                    </a>
                @endforeach
            </div>

            {{-- ── CHARTS + ACTIVITY ROW ───────────────────────── --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- Left: Charts (2/3 width) --}}
                <div class="xl:col-span-2 space-y-6">

                    {{-- Trend + Status side by side --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Installation Trend</h3>
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Last 14 days</span>
                            </div>
                            <div style="height:200px"><canvas id="trendChart"></canvas></div>
                        </div>
                        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Meter Status</h3>
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">Distribution</span>
                            </div>
                            <div style="height:200px"><canvas id="statusChart"></canvas></div>
                        </div>
                    </div>

                    {{-- Worker Performance --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Worker Performance</h3>
                            <a href="{{ route('reports.index') }}" class="text-xs text-indigo-600 hover:underline">View report →</a>
                        </div>
                        <div style="height:220px"><canvas id="workerChart"></canvas></div>
                    </div>

                    {{-- Lot Completion --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Lot Completion</h3>
                            <a href="{{ route('lots.index') }}" class="text-xs text-indigo-600 hover:underline">All lots →</a>
                        </div>
                        <div style="height:240px"><canvas id="lotChart"></canvas></div>
                    </div>
                </div>

                {{-- Right: Activity Feed (1/3 width) --}}
                <div class="space-y-6">

                    {{-- Worker Leaderboard --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Worker Leaderboard</h3>
                        <div class="space-y-2">
                            @foreach($workers->sortByDesc('installed_count')->take(6) as $i => $worker)
                                <a href="{{ route('workers.show', $worker) }}"
                                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500')) }}">
                                        {{ $i + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600">{{ $worker->name }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                @php $maxInstalled = $workers->max('installed_count') ?: 1; @endphp
                                                <div class="h-full bg-indigo-500 rounded-full" style="width:{{ ($worker->installed_count / $maxInstalled) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="text-sm font-bold text-indigo-600">{{ $worker->installed_count }}</div>
                                        <div class="text-xs text-gray-400">installed</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent Assignments --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Recent Assignments</h3>
                            <a href="{{ route('assignments.create') }}" class="text-xs text-indigo-600 hover:underline">Assign →</a>
                        </div>
                        @if($recentAssignments->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-4">No assignments yet.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($recentAssignments->take(6) as $assign)
                                    <div class="flex items-start justify-between py-2 border-b border-gray-50 last:border-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('stock.show', $assign->meter) }}" class="text-sm font-medium text-indigo-700 hover:underline">{{ $assign->meter->meter_number }}</a>
                                            <div class="text-xs text-gray-400 mt-0.5">→ {{ $assign->worker->name }}</div>
                                        </div>
                                        <span class="text-xs text-gray-400 shrink-0 ml-2">{{ $assign->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Pending Returns --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-800">Return Requests</h3>
                            <a href="{{ route('returns.index') }}" class="text-xs text-indigo-600 hover:underline">All →</a>
                        </div>
                        @if($recentReturns->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-4">No return requests.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($recentReturns->take(5) as $ret)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('stock.show', $ret->meter) }}" class="text-sm font-medium text-indigo-700 hover:underline">{{ $ret->meter->meter_number }}</a>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $ret->worker->name }}</div>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium shrink-0 ml-2
                                            {{ $ret->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $ret->status === 'approved' ? 'bg-green-100 text-green-700'  : '' }}
                                            {{ $ret->status === 'rejected' ? 'bg-red-100 text-red-700'      : '' }}">
                                            {{ ucfirst($ret->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Install Type Split --}}
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Installation Types</h3>
                        @php
                            $totalInstalls = $stats['nsc_installs'] + $stats['replacement_installs'];
                            $nscPct = $totalInstalls > 0 ? round(($stats['nsc_installs'] / $totalInstalls) * 100) : 0;
                        @endphp
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span class="font-medium">NSC</span>
                                    <span class="font-bold text-indigo-600">{{ number_format($stats['nsc_installs']) }}</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full"><div class="h-full bg-indigo-500 rounded-full" style="width:{{ $nscPct }}%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span class="font-medium">Replacement</span>
                                    <span class="font-bold text-orange-600">{{ number_format($stats['replacement_installs']) }}</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full"><div class="h-full bg-orange-500 rounded-full" style="width:{{ 100 - $nscPct }}%"></div></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const d = @json($chartData);
            const fontBase = { family: "'Inter', sans-serif", size: 11 };

            Chart.defaults.font = fontBase;
            Chart.defaults.color = '#6b7280';

            // Trend
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: { labels: d.trendLabels, datasets: [{ label: 'Installed', data: d.trendData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#6366f1' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } }, x: { grid: { display: false }, ticks: { maxRotation: 0, maxTicksLimit: 7 } } } }
            });

            // Status doughnut
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: { labels: d.statusLabels, datasets: [{ data: d.statusData, backgroundColor: ['#22c55e','#3b82f6','#6366f1','#ef4444','#6b7280'], borderWidth: 3, borderColor: '#fff' }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 10 } } } } }
            });

            // Worker bar
            new Chart(document.getElementById('workerChart'), {
                type: 'bar',
                data: { labels: d.workerLabels, datasets: [
                    { label: 'Assigned',  data: d.workerAssigned,  backgroundColor: 'rgba(59,130,246,0.75)',  borderRadius: 5, borderSkipped: false },
                    { label: 'Installed', data: d.workerInstalled, backgroundColor: 'rgba(16,185,129,0.75)', borderRadius: 5, borderSkipped: false }
                ]},
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } } }
            });

            // Lot horizontal bar
            new Chart(document.getElementById('lotChart'), {
                type: 'bar',
                data: { labels: d.lotLabels, datasets: [{ label: 'Completion %', data: d.lotData, backgroundColor: d.lotData.map(v => v >= 100 ? 'rgba(34,197,94,0.75)' : v > 50 ? 'rgba(234,179,8,0.75)' : 'rgba(99,102,241,0.75)'), borderRadius: 4 }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.parsed.x + '%' } } }, scales: { x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#f3f4f6' } }, y: { grid: { display: false } } } }
            });
        });
    </script>
    @endpush
</x-app-layout>
