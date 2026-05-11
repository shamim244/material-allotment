<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Return Requests') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-0">
                <a href="{{ route('returns.index', ['status' => 'all']) }}"
                   class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-indigo-500 hover:shadow-md transition {{ request('status') === 'all' ? 'ring-2 ring-indigo-400' : '' }}">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total</div>
                </a>
                <a href="{{ route('returns.index', ['status' => 'pending']) }}"
                   class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-yellow-500 hover:shadow-md transition {{ (!request('status') || request('status') === 'pending') ? 'ring-2 ring-yellow-400' : '' }}">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pending</div>
                </a>
                <a href="{{ route('returns.index', ['status' => 'approved']) }}"
                   class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-green-500 hover:shadow-md transition {{ request('status') === 'approved' ? 'ring-2 ring-green-400' : '' }}">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Approved</div>
                </a>
                <a href="{{ route('returns.index', ['status' => 'rejected']) }}"
                   class="bg-white rounded-xl shadow-sm p-4 text-center border-t-4 border-red-500 hover:shadow-md transition {{ request('status') === 'rejected' ? 'ring-2 ring-red-400' : '' }}">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Rejected</div>
                </a>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white rounded-xl shadow-sm px-4 sm:px-0">
                <div class="p-4">
                    <form method="GET" action="{{ route('returns.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="🔍 Search by meter number or worker name...">
                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('returns.index', ['status' => request('status', 'pending')]) }}"
                               class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Return Requests List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-4 sm:p-6">

                    @if($requests->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            @if(request('search'))
                                No return requests found for "{{ request('search') }}".
                            @else
                                No {{ request('status', 'pending') }} return requests.
                            @endif
                        </div>
                    @else
                        {{-- Mobile Cards --}}
                        <div class="sm:hidden space-y-3">
                            @foreach($requests as $req)
                                <div class="border rounded-xl p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <a href="{{ route('stock.show', $req->meter) }}" class="font-bold text-indigo-700 text-lg hover:underline">{{ $req->meter->meter_number }}</a>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                👷 <a href="{{ route('workers.show', $req->worker) }}" class="text-indigo-600 hover:underline">{{ $req->worker->name }}</a>
                                            </div>
                                        </div>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $req->reason }}</div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400">{{ $req->created_at->format('d M Y, h:i A') }}</span>
                                        @if($req->status === 'pending')
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('returns.update', $req->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button onclick="return confirm('Approve this return?')" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md font-medium hover:bg-green-200 transition">✓ Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('returns.update', $req->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <input type="hidden" name="admin_remark" value="Rejected by admin">
                                                <button onclick="return confirm('Reject this return?')" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md font-medium hover:bg-red-200 transition">✕ Reject</button>
                                            </form>
                                        </div>
                                        @endif
                                        @if($req->admin_remark)
                                            <span class="text-xs text-gray-500 italic">{{ $req->admin_remark }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desktop Table --}}
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($requests as $req)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('stock.show', $req->meter) }}" class="text-indigo-600 hover:underline">{{ $req->meter->meter_number }}</a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <a href="{{ route('workers.show', $req->worker) }}" class="text-indigo-600 hover:underline">{{ $req->worker->name }}</a>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $req->reason }}</td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'approved' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100' }}">
                                                    {{ ucfirst($req->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                                {{ $req->created_at->format('d M Y, h:i A') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                @if($req->status === 'pending')
                                                <div class="flex justify-end gap-2">
                                                    <form method="POST" action="{{ route('returns.update', $req->id) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button onclick="return confirm('Approve return? This will credit stock.')" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md font-medium hover:bg-green-200 transition">✓ Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('returns.update', $req->id) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <input type="hidden" name="admin_remark" value="Rejected by admin">
                                                        <button onclick="return confirm('Reject this return?')" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-md font-medium hover:bg-red-200 transition">✕ Reject</button>
                                                    </form>
                                                </div>
                                                @elseif($req->admin_remark)
                                                    <span class="text-xs text-gray-400 italic">{{ $req->admin_remark }}</span>
                                                @else
                                                    <span class="text-xs text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $requests->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
