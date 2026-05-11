<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('stock.groups') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Success/Error -->
            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif

            <!-- Group Info -->
            <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $group->name }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium 
                                {{ $group->type === 'box' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $group->type === 'large' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $group->type === 'normal' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($group->type) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $group->meters->count() }} meters</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-400">
                        Created {{ $group->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>

            <!-- Meters in Group -->
            <!-- Mobile Card View -->
            <div class="sm:hidden space-y-3 px-4">
                @foreach($group->meters as $meter)
                    <a href="{{ route('stock.show', $meter) }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-indigo-700">{{ $meter->meter_number }}</div>
                            @php
                                $statusColors = [
                                    'stock' => 'bg-green-100 text-green-700',
                                    'assigned' => 'bg-blue-100 text-blue-700',
                                    'installed' => 'bg-emerald-100 text-emerald-700',
                                    'issue' => 'bg-red-100 text-red-700',
                                    'returned' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($meter->status) }}
                            </span>
                        </div>
                        @if($meter->currentWorker)
                            <div class="text-xs text-gray-500 mt-1">→ <a href="{{ route('workers.show', $meter->currentWorker) }}" class="hover:underline text-indigo-500" @click.stop>{{ $meter->currentWorker->name }}</a></div>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Desktop Table -->
            <div class="hidden sm:block bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($group->meters as $index => $meter)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('stock.show', $meter) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $meter->meter_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'stock' => 'bg-green-100 text-green-700',
                                            'assigned' => 'bg-blue-100 text-blue-700',
                                            'installed' => 'bg-emerald-100 text-emerald-700',
                                            'issue' => 'bg-red-100 text-red-700',
                                            'returned' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($meter->currentWorker)
                                        <a href="{{ route('workers.show', $meter->currentWorker) }}" class="text-indigo-600 hover:underline">{{ $meter->currentWorker->name }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('stock.show', $meter) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
