<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🚨</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Action Required') }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Priority Stats --}}
            @php
                $filterBase = array_filter(['worker_id' => request('worker_id'), 'search' => request('search')]);
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-0">
                <a href="{{ route('action-required', array_merge($filterBase, ['priority' => 'all'])) }}"
                   class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-gray-500 hover:shadow-md transition {{ $activeTab === 'all' ? 'ring-2 ring-gray-400' : '' }}">
                    <div class="text-3xl font-bold text-gray-700">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1 font-medium">All Open</div>
                </a>
                <a href="{{ route('action-required', array_merge($filterBase, ['priority' => 'job_not_found'])) }}"
                   class="relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-4 text-center border-l-4 border-purple-600 hover:shadow-md transition {{ $activeTab === 'job_not_found' ? 'ring-2 ring-purple-400' : '' }}">
                    @if($stats['job_not_found'] > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-purple-500 rounded-full animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-purple-600 rounded-full"></span>
                    @endif
                    <div class="text-3xl font-bold text-purple-700">{{ $stats['job_not_found'] }}</div>
                    <div class="text-xs text-purple-600 mt-1 font-medium">🔍 Job Not Found</div>
                </a>
                <a href="{{ route('action-required', array_merge($filterBase, ['priority' => 'issue'])) }}"
                   class="relative bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-sm p-4 text-center border-l-4 border-red-600 hover:shadow-md transition {{ $activeTab === 'issue' ? 'ring-2 ring-red-400' : '' }}">
                    @if($stats['issue'] > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-600 rounded-full"></span>
                    @endif
                    <div class="text-3xl font-bold text-red-700">{{ $stats['issue'] }}</div>
                    <div class="text-xs text-red-600 mt-1 font-medium">⚠️ Issues</div>
                </a>
                <a href="{{ route('action-required', array_merge($filterBase, ['priority' => 'pending'])) }}"
                   class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-sm p-4 text-center border-l-4 border-yellow-500 hover:shadow-md transition {{ $activeTab === 'pending' ? 'ring-2 ring-yellow-400' : '' }}">
                    <div class="text-3xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-yellow-600 mt-1 font-medium">⏳ Pending</div>
                </a>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-sm px-4 sm:px-0">
                <div class="p-4">
                    <form method="GET" action="{{ route('action-required') }}" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="priority" value="{{ $activeTab }}">
                        @if(Auth::user()->role === 'contractor')
                            <select name="worker_id" class="rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500 sm:w-48">
                                <option value="">All Workers</option>
                                @foreach($workers as $w)
                                    <option value="{{ $w->id }}" {{ request('worker_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="🔍 Search meter number...">
                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Filter</button>
                        @if(request()->hasAny(['worker_id', 'search']))
                            <a href="{{ route('action-required', ['priority' => $activeTab]) }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Records --}}
            @if($records->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-8 text-center mx-4 sm:mx-0">
                    <div class="text-5xl mb-3">✅</div>
                    <div class="text-lg font-semibold text-gray-700">All Clear!</div>
                    <div class="text-sm text-gray-500 mt-1">No action required items found.</div>
                </div>
            @else
                <div class="space-y-3 px-4 sm:px-0">
                    @foreach($records as $record)
                        @php
                            $priorityConfig = [
                                'job_not_found' => [
                                    'border' => 'border-l-purple-600',
                                    'bg'     => 'bg-purple-50',
                                    'badge'  => 'bg-purple-100 text-purple-800',
                                    'label'  => '🔍 Job Not Found',
                                    'icon'   => '🔍',
                                    'urgency' => 'HIGH PRIORITY',
                                    'urgBg'  => 'bg-purple-600',
                                ],
                                'issue' => [
                                    'border' => 'border-l-red-500',
                                    'bg'     => 'bg-red-50',
                                    'badge'  => 'bg-red-100 text-red-800',
                                    'label'  => '⚠️ Issue',
                                    'icon'   => '⚠️',
                                    'urgency' => 'NEEDS ATTENTION',
                                    'urgBg'  => 'bg-red-500',
                                ],
                                'pending' => [
                                    'border' => 'border-l-yellow-500',
                                    'bg'     => 'bg-yellow-50',
                                    'badge'  => 'bg-yellow-100 text-yellow-800',
                                    'label'  => '⏳ Pending',
                                    'icon'   => '⏳',
                                    'urgency' => 'AWAITING',
                                    'urgBg'  => 'bg-yellow-500',
                                ],
                            ];
                            $cfg = $priorityConfig[$record->mi_status] ?? $priorityConfig['pending'];
                            $detail = $record->meter->installationDetail;
                        @endphp

                        <div class="bg-white rounded-xl shadow-sm border-l-4 {{ $cfg['border'] }} hover:shadow-md transition overflow-hidden">
                            {{-- Urgency Bar --}}
                            <div class="flex items-center justify-between px-4 py-2 {{ $cfg['bg'] }}">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold {{ $cfg['badge'] }} rounded-full">
                                        {{ $cfg['label'] }}
                                    </span>
                                    @if($record->issueType)
                                        <span class="text-xs text-gray-600">— {{ $record->issueType->name }}</span>
                                    @endif
                                    @if($record->remark)
                                        <span class="text-xs text-gray-500 hidden sm:inline">• {{ Str::limit($record->remark, 60) }}</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $cfg['urgBg'] }} text-white px-2 py-0.5 rounded-full font-bold tracking-wider hidden sm:inline">
                                    {{ $cfg['urgency'] }}
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    {{-- Meter Info --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <a href="{{ route('mi.show', $record) }}" class="text-lg font-bold text-indigo-700 hover:underline">
                                                {{ $record->meter->meter_number }}
                                            </a>
                                            @if($detail)
                                                @if($detail->install_type === 'replacement')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 font-medium">🔄 RPL</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 font-medium">🆕 NSC</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                                            <span>👷 <a href="{{ route('workers.show', $record->worker) }}" class="text-indigo-600 hover:underline font-medium">{{ $record->worker->name }}</a></span>
                                            @if($record->meter->group)
                                                <span>📦 <a href="{{ route('stock.groups.show', $record->meter->group) }}" class="text-indigo-600 hover:underline">{{ $record->meter->group->name }}</a></span>
                                            @endif
                                            @if($detail?->mobile_number)
                                                <span>📱 {{ $detail->mobile_number }}</span>
                                            @endif
                                            @if($detail?->install_type === 'replacement' && $detail?->ca_number)
                                                <span>🏷️ CA: {{ $detail->ca_number }}</span>
                                            @endif
                                            @if($detail?->install_type === 'nsc' && $detail?->request_number)
                                                <span>🏷️ Req: {{ $detail->request_number }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 sm:flex-col sm:items-end">
                                        <span class="text-xs text-gray-400">{{ $record->updated_at->diffForHumans() }}</span>
                                        <a href="{{ route('mi.show', $record) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                                            View Details →
                                        </a>
                                    </div>
                                </div>

                                @if($record->remark)
                                    <div class="mt-2 sm:hidden text-xs text-gray-500 bg-gray-50 rounded p-2">
                                        💬 {{ $record->remark }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($records->hasPages())
                    <div class="px-4 sm:px-0 mt-4">
                        {{ $records->withQueryString()->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
