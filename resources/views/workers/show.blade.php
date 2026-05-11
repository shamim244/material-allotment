<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👷 {{ $user->name }}
            </h2>
            <span class="text-sm text-gray-400">{{ $user->email }}</span>
            @if($user->mobile)
                <span class="text-sm text-gray-400">· {{ $user->mobile }}</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Worker Info Card --}}
            <div class="bg-white rounded-lg shadow-sm mx-4 sm:mx-0 p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $user->name }}</div>
                        <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
                            @if($user->email)
                                <span>📧 {{ $user->email }}</span>
                            @endif
                            @if($user->mobile)
                                <span>📱 {{ $user->mobile }}</span>
                            @endif
                            <span>📅 Joined {{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 text-xs rounded-full font-medium {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <a href="{{ route('assignments.challan', $user) }}" target="_blank" class="px-3 py-1 text-xs bg-amber-100 text-amber-700 rounded-full font-medium hover:bg-amber-200">
                            🖨️ Print Challan
                        </a>
                        <a href="{{ route('worker.dashboard', ['worker_id' => $user->id]) }}" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-full font-medium hover:bg-indigo-200">
                            View as Assign Dashboard →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mx-4 sm:mx-0">
                <div class="bg-gray-50 rounded-lg p-3 text-center shadow-sm">
                    <div class="text-xs uppercase text-gray-500">Total Assigned</div>
                    <div class="text-xl font-bold text-gray-700">{{ $stats['total_assigned'] }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-3 text-center shadow-sm">
                    <div class="text-xs uppercase text-blue-600">Currently Held</div>
                    <div class="text-xl font-bold text-blue-700">{{ $stats['held'] }}</div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center shadow-sm">
                    <div class="text-xs uppercase text-emerald-600">Installed</div>
                    <div class="text-xl font-bold text-emerald-700">{{ $stats['installed'] }}</div>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center shadow-sm">
                    <div class="text-xs uppercase text-red-600">Issues</div>
                    <div class="text-xl font-bold text-red-700">{{ $stats['issues'] }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center shadow-sm">
                    <div class="text-xs uppercase text-gray-500">Returned</div>
                    <div class="text-xl font-bold text-gray-600">{{ $stats['returned'] }}</div>
                </div>
            </div>

            {{-- MI Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mx-4 sm:mx-0">
                <div class="bg-yellow-50 rounded-lg p-3 text-center shadow-sm border border-yellow-200">
                    <div class="text-xs uppercase text-yellow-600">MI Pending</div>
                    <div class="text-xl font-bold text-yellow-700">{{ $miStats['pending'] }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center shadow-sm border border-green-200">
                    <div class="text-xs uppercase text-green-600">MI Complete</div>
                    <div class="text-xl font-bold text-green-700">{{ $miStats['complete'] }}</div>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center shadow-sm border border-red-200">
                    <div class="text-xs uppercase text-red-600">MI Issue</div>
                    <div class="text-xl font-bold text-red-700">{{ $miStats['issue'] }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center shadow-sm border border-purple-200">
                    <div class="text-xs uppercase text-purple-600">Job Not Found</div>
                    <div class="text-xl font-bold text-purple-700">{{ $miStats['job_not_found'] }}</div>
                </div>
            </div>

            {{-- Currently Held Meters --}}
            @if($heldMeters->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm mx-4 sm:mx-0 overflow-hidden">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">📦 Currently Held Meters ({{ $heldMeters->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-2">Meter #</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Group</th>
                                    <th class="px-4 py-2">Lot</th>
                                    <th class="px-4 py-2">Since</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($heldMeters as $meter)
                                    @php
                                        $sc = ['assigned' => 'bg-blue-100 text-blue-700', 'issue' => 'bg-red-100 text-red-700'];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">
                                            <a href="{{ route('stock.show', $meter) }}" class="font-medium text-indigo-600 hover:underline">{{ $meter->meter_number }}</a>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($meter->status) }}</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->group)
                                                <a href="{{ route('stock.groups.show', $meter->group) }}" class="text-indigo-600 hover:underline text-xs">{{ $meter->group->name }}</a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->lot)
                                                <a href="{{ route('lots.show', $meter->lot) }}" class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium hover:bg-indigo-100">{{ $meter->lot->lot_number }}</a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-400">{{ $meter->updated_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recently Installed --}}
            @if($installedMeters->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm mx-4 sm:mx-0 overflow-hidden">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">✅ Recently Installed ({{ $installedMeters->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-2">Meter #</th>
                                    <th class="px-4 py-2">Type</th>
                                    <th class="px-4 py-2">MI Status</th>
                                    <th class="px-4 py-2">Group</th>
                                    <th class="px-4 py-2">Lot</th>
                                    <th class="px-4 py-2">Installed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($installedMeters as $meter)
                                    @php
                                        $miColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'complete' => 'bg-green-100 text-green-700', 'issue' => 'bg-red-100 text-red-700', 'job_not_found' => 'bg-purple-100 text-purple-700'];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">
                                            <a href="{{ route('stock.show', $meter) }}" class="font-medium text-indigo-600 hover:underline">{{ $meter->meter_number }}</a>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->installationDetail?->install_type === 'replacement')
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">🔄 RPL</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">🆕 NSC</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->activation)
                                                <a href="{{ route('mi.show', $meter->activation) }}" class="px-2 py-0.5 text-xs rounded-full {{ $miColors[$meter->activation->mi_status] ?? 'bg-gray-100' }} hover:opacity-80">
                                                    {{ $meter->activation->mi_status === 'job_not_found' ? 'JNF' : ucfirst($meter->activation->mi_status) }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->group)
                                                <a href="{{ route('stock.groups.show', $meter->group) }}" class="text-indigo-600 hover:underline text-xs">{{ $meter->group->name }}</a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($meter->lot)
                                                <a href="{{ route('lots.show', $meter->lot) }}" class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium hover:bg-indigo-100">{{ $meter->lot->lot_number }}</a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-400">{{ $meter->updated_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent MI Records --}}
            @if($miRecords->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm mx-4 sm:mx-0 overflow-hidden">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">⚡ Recent MI Records</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($miRecords as $mi)
                            @php
                                $miColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'complete' => 'bg-green-100 text-green-700', 'issue' => 'bg-red-100 text-red-700', 'job_not_found' => 'bg-purple-100 text-purple-700'];
                            @endphp
                            <a href="{{ route('mi.show', $mi) }}" class="border rounded-lg p-3 hover:shadow-md transition hover:border-indigo-300">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-mono font-bold text-indigo-700 text-sm">{{ $mi->meter->meter_number }}</span>
                                    <div class="flex items-center gap-1">
                                        @if($mi->meter->installationDetail?->install_type === 'replacement')
                                            <span class="px-1.5 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700">🔄</span>
                                        @else
                                            <span class="px-1.5 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">🆕</span>
                                        @endif
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $miColors[$mi->mi_status] ?? 'bg-gray-100' }}">
                                            {{ $mi->mi_status === 'job_not_found' ? 'JNF' : ucfirst($mi->mi_status) }}
                                        </span>
                                    </div>
                                </div>
                                @if($mi->remark)
                                    <div class="text-xs text-gray-500 truncate">{{ $mi->remark }}</div>
                                @endif
                                <div class="text-xs text-gray-400 mt-1">{{ $mi->updated_at->diffForHumans() }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
