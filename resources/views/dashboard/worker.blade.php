<x-app-layout>
    <x-slot name="header">My Dashboard</x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="p-6 max-w-4xl mx-auto space-y-6">

            {{-- ── WELCOME BANNER ──────────────────────────────── --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-[#1e40af] via-[#1d4ed8] to-[#3b82f6] rounded-2xl px-6 py-5 shadow-lg">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSA2MCAwIEwgMCAwIDAgNjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC4zIiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZykiLz48L3N2Zz4=')] opacity-30 pointer-events-none"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-blue-200 text-sm font-medium">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
                        <h1 class="text-white text-2xl font-bold mt-0.5">{{ Auth::user()->name }}</h1>
                        <p class="text-blue-200 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
                    </div>
                    <div class="hidden sm:flex gap-3">
                        <a href="{{ route('worker.dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded-xl transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                            My Meters
                        </a>
                        <a href="{{ route('mi.index') }}" class="bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded-xl transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            MI Jobs
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── METER KPIs ───────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $kpis = [
                        ['label'=>'Assigned',       'val'=>$stats['assigned'],       'color'=>'text-blue-600',   'bg'=>'bg-blue-50',   'ring'=>'ring-blue-200',   'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['label'=>'Installed',      'val'=>$stats['installed'],      'color'=>'text-green-600',  'bg'=>'bg-green-50',  'ring'=>'ring-green-200',  'icon'=>'M5 13l4 4L19 7'],
                        ['label'=>'Issues',         'val'=>$stats['issues'],         'color'=>'text-red-600',    'bg'=>'bg-red-50',    'ring'=>'ring-red-200',    'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ['label'=>'Rtn Pending',    'val'=>$stats['return_pending'], 'color'=>'text-orange-600', 'bg'=>'bg-orange-50', 'ring'=>'ring-orange-200', 'icon'=>'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
                    ];
                @endphp
                @foreach($kpis as $k)
                    <div class="{{ $k['bg'] }} ring-1 {{ $k['ring'] }} rounded-2xl p-5 flex flex-col gap-2">
                        <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 {{ $k['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-3xl font-bold {{ $k['color'] }}">{{ number_format($k['val']) }}</div>
                            <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $k['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── MI STATUS ────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Meter Installation (MI) Status</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $miItems = [
                            ['label'=>'MI Pending',   'val'=>$stats['mi_pending'],       'status'=>'pending',       'color'=>'text-amber-700',  'bg'=>'bg-amber-50',  'ring'=>'ring-amber-200'],
                            ['label'=>'MI Issues',    'val'=>$stats['mi_issue'],         'status'=>'issue',         'color'=>'text-red-700',    'bg'=>'bg-red-50',    'ring'=>'ring-red-200'],
                            ['label'=>'MI Complete',  'val'=>$stats['mi_complete'],      'status'=>'complete',      'color'=>'text-green-700',  'bg'=>'bg-green-50',  'ring'=>'ring-green-200'],
                            ['label'=>'Not Found',    'val'=>$stats['mi_job_not_found'], 'status'=>'job_not_found', 'color'=>'text-slate-600',  'bg'=>'bg-slate-50',  'ring'=>'ring-slate-200'],
                        ];
                    @endphp
                    @foreach($miItems as $m)
                        <a href="{{ route('mi.index', ['status' => $m['status']]) }}"
                           class="{{ $m['bg'] }} ring-1 {{ $m['ring'] }} rounded-xl p-3 text-center hover:shadow-md transition hover:-translate-y-0.5">
                            <div class="text-2xl font-bold {{ $m['color'] }}">{{ number_format($m['val']) }}</div>
                            <div class="text-xs text-gray-500 mt-1 font-medium">{{ $m['label'] }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── PROGRESS OVERVIEW ────────────────────────────── --}}
            @php
                $total = $stats['assigned'] + $stats['installed'] + $stats['issues'] + $stats['return_pending'];
                $installPct = $total > 0 ? round(($stats['installed'] / $total) * 100) : 0;
                $issuePct   = $total > 0 ? round(($stats['issues']    / $total) * 100) : 0;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">My Progress</h3>
                    <span class="text-sm font-bold text-indigo-600">{{ $installPct }}% installed</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-green-500 transition-all" style="width:{{ $installPct }}%"></div>
                    <div class="h-full bg-red-400 transition-all" style="width:{{ $issuePct }}%"></div>
                    <div class="h-full bg-blue-400 flex-1"></div>
                </div>
                <div class="flex items-center gap-5 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>Installed {{ $installPct }}%</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>Issues {{ $issuePct }}%</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>Active</span>
                </div>
            </div>

            {{-- ── RECENT ACTIVITY ──────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Meter Activity</h3>
                    <a href="{{ route('worker.dashboard') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
                </div>
                @if($recentMeters->isEmpty())
                    <div class="py-8 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-sm">No meters assigned yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($recentMeters as $meter)
                            <div class="flex items-center justify-between py-3">
                                <div class="font-medium text-sm text-gray-900">{{ $meter->meter_number }}</div>
                                <div class="flex items-center gap-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                        {{ $meter->status === 'assigned'      ? 'bg-blue-100 text-blue-700'    : '' }}
                                        {{ $meter->status === 'installed'     ? 'bg-green-100 text-green-700'  : '' }}
                                        {{ $meter->status === 'issue'         ? 'bg-red-100 text-red-700'      : '' }}
                                        {{ $meter->status === 'return_pending'? 'bg-orange-100 text-orange-700': '' }}
                                        {{ $meter->status === 'mi_complete'   ? 'bg-emerald-100 text-emerald-700': '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $meter->status)) }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $meter->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── QUICK ACTIONS ────────────────────────────────── --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('worker.dashboard') }}"
                   class="flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl px-5 py-4 transition group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    <div>
                        <div class="text-sm font-semibold">My Meters</div>
                        <div class="text-xs text-indigo-200">View & update status</div>
                    </div>
                </a>
                <a href="{{ route('mi.index') }}"
                   class="flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl px-5 py-4 transition group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <div>
                        <div class="text-sm font-semibold">MI Dashboard</div>
                        <div class="text-xs text-emerald-200">Manage installations</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
