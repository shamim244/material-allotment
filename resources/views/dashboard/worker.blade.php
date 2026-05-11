<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-4 sm:px-0">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['assigned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Assigned</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['installed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Installed</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $stats['issues'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Issues</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-3xl font-bold text-orange-500">{{ $stats['return_pending'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Return Pending</div>
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

            <!-- Quick Actions -->
            <div class="px-4 sm:px-0 flex flex-wrap gap-2">
                <a href="{{ route('worker.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    View My Meters →
                </a>
                <a href="{{ route('mi.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                    MI Dashboard →
                </a>
            </div>

            <!-- Recent Meter Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    @if($recentMeters->isEmpty())
                        <p class="text-gray-500 text-sm">No meters assigned yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($recentMeters as $meter)
                                <div class="flex justify-between items-center border-b border-gray-100 py-2 text-sm">
                                    <div>
                                        <span class="font-medium text-indigo-700">{{ $meter->meter_number }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $meter->status === 'assigned' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $meter->status === 'installed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $meter->status === 'issue' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $meter->status === 'return_pending' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $meter->status === 'mi_complete' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $meter->status === 'mi_issue' ? 'bg-rose-100 text-rose-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $meter->status)) }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $meter->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
