<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contractor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 px-4 sm:px-0">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_meters'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Meters</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['in_stock'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">In Stock</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['assigned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Assigned</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-emerald-600">{{ $stats['installed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Installed</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $stats['issues'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Issues</div>
                </div>
            </div>

            <!-- Secondary Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-4 sm:px-0">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-orange-500">{{ $stats['return_pending'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Return Pending</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['returned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Returned</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_workers'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Workers</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-500">{{ $stats['total_groups'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Groups</div>
                </div>
            </div>

            <!-- MI Stats -->
            <div class="grid grid-cols-3 gap-4 px-4 sm:px-0">
                <a href="{{ route('mi.index', ['status' => 'pending']) }}" class="bg-yellow-50 rounded-lg shadow p-4 text-center border border-yellow-200 hover:shadow-md transition">
                    <div class="text-2xl font-bold text-yellow-700">{{ $stats['mi_pending'] }}</div>
                    <div class="text-xs text-yellow-600 mt-1">MI Pending</div>
                </a>
                <a href="{{ route('mi.index', ['status' => 'issue']) }}" class="bg-red-50 rounded-lg shadow p-4 text-center border border-red-200 hover:shadow-md transition">
                    <div class="text-2xl font-bold text-red-700">{{ $stats['mi_issue'] }}</div>
                    <div class="text-xs text-red-600 mt-1">MI Issue</div>
                </a>
                <a href="{{ route('mi.index', ['status' => 'complete']) }}" class="bg-green-50 rounded-lg shadow p-4 text-center border border-green-200 hover:shadow-md transition">
                    <div class="text-2xl font-bold text-green-700">{{ $stats['mi_complete'] }}</div>
                    <div class="text-xs text-green-600 mt-1">MI Complete</div>
                </a>
            </div>

            <!-- Installation Type Stats -->
            <div class="grid grid-cols-2 gap-4 px-4 sm:px-0">
                <div class="bg-indigo-50 rounded-lg shadow p-4 text-center border border-indigo-200">
                    <div class="text-2xl font-bold text-indigo-700">{{ $stats['nsc_installs'] }}</div>
                    <div class="text-xs text-indigo-600 mt-1">🆕 NSC Installations</div>
                </div>
                <div class="bg-orange-50 rounded-lg shadow p-4 text-center border border-orange-200">
                    <div class="text-2xl font-bold text-orange-700">{{ $stats['replacement_installs'] }}</div>
                    <div class="text-xs text-orange-600 mt-1">🔄 Replacement Installations</div>
                </div>
            </div>

            <!-- ═══ CHARTS SECTION ═══ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-4 sm:px-0">

                <!-- Meter Status Distribution (Doughnut) -->
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">📊 Meter Status Distribution</h3>
                    <div class="relative" style="max-height: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Installation Trend (Line) -->
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">📈 Installation Trend (Last 14 Days)</h3>
                    <div class="relative" style="max-height: 280px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <!-- Worker Comparison (Bar) -->
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">👷 Worker Performance</h3>
                    <div class="relative" style="max-height: 280px;">
                        <canvas id="workerChart"></canvas>
                    </div>
                </div>

                <!-- Lot Completion (Horizontal Bar) -->
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">📦 Lot Completion Progress</h3>
                    <div class="relative" style="max-height: 280px;">
                        <canvas id="lotChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- Worker Performance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Worker Performance</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assigned</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Installed</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issues</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">MI ✓</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($workers as $worker)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium"><a href="{{ route('workers.show', $worker) }}" class="text-indigo-600 hover:underline">{{ $worker->name }}</a></td>
                                        <td class="px-4 py-3 text-sm text-center text-blue-600 font-semibold">{{ $worker->assigned_count }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-green-600 font-semibold">{{ $worker->installed_count }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-red-600 font-semibold">{{ $worker->issue_count }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-emerald-600 font-semibold">{{ $worker->mi_complete_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Assignments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Assignments</h3>
                    @if($recentAssignments->isEmpty())
                        <p class="text-gray-500 text-sm">No assignments yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($recentAssignments as $assign)
                                <div class="flex justify-between items-center border-b border-gray-100 py-2 text-sm">
                                    <div>
                                        <a href="{{ route('stock.show', $assign->meter) }}" class="font-medium text-indigo-700 hover:underline">{{ $assign->meter->meter_number }}</a>
                                        <span class="text-gray-400 mx-1">→</span>
                                        <a href="{{ route('workers.show', $assign->worker) }}" class="text-gray-700 hover:text-indigo-600 hover:underline">{{ $assign->worker->name }}</a>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $assign->created_at->diffForHumans() }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Returns -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Return Requests</h3>
                    @if($recentReturns->isEmpty())
                        <p class="text-gray-500 text-sm">No return requests yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($recentReturns as $ret)
                                <div class="flex justify-between items-center border-b border-gray-100 py-2 text-sm">
                                    <div>
                                        <a href="{{ route('stock.show', $ret->meter) }}" class="font-medium text-indigo-700 hover:underline">{{ $ret->meter->meter_number }}</a>
                                        <span class="text-gray-400 mx-1">by</span>
                                        <a href="{{ route('workers.show', $ret->worker) }}" class="text-gray-700 hover:text-indigo-600 hover:underline">{{ $ret->worker->name }}</a>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $ret->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $ret->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $ret->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($ret->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);

            // ── Doughnut: Meter Status Distribution ──
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: chartData.statusLabels,
                    datasets: [{
                        data: chartData.statusData,
                        backgroundColor: ['#22c55e', '#3b82f6', '#10b981', '#ef4444', '#6b7280'],
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } }
                    }
                }
            });

            // ── Line: Installation Trend ──
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: chartData.trendLabels,
                    datasets: [{
                        label: 'Installations',
                        data: chartData.trendData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#6366f1',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { ticks: { maxRotation: 45 } }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            // ── Bar: Worker Comparison ──
            new Chart(document.getElementById('workerChart'), {
                type: 'bar',
                data: {
                    labels: chartData.workerLabels,
                    datasets: [
                        {
                            label: 'Assigned',
                            data: chartData.workerAssigned,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Installed',
                            data: chartData.workerInstalled,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } }
                    }
                }
            });

            // ── Horizontal Bar: Lot Completion ──
            new Chart(document.getElementById('lotChart'), {
                type: 'bar',
                data: {
                    labels: chartData.lotLabels,
                    datasets: [{
                        label: 'Completion %',
                        data: chartData.lotData,
                        backgroundColor: chartData.lotData.map(v => v >= 100 ? 'rgba(34, 197, 94, 0.7)' : v > 50 ? 'rgba(234, 179, 8, 0.7)' : 'rgba(99, 102, 241, 0.7)'),
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ctx.parsed.x + '%' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
