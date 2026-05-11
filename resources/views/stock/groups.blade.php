<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock.index') }}" class="text-indigo-600 hover:text-indigo-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Groups / Boxes') }}
                </h2>
            </div>
            <a href="{{ route('stock.import') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                + Import
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Success/Error -->
            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-4 sm:px-0">
                <div class="bg-white rounded-lg shadow p-3 text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Groups</div>
                </div>
                <a href="{{ route('stock.groups', ['type' => 'box']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('type') === 'box' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['box'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Box</div>
                </a>
                <a href="{{ route('stock.groups', ['type' => 'large']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('type') === 'large' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['large'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Large Batch</div>
                </a>
                <a href="{{ route('stock.groups', ['type' => 'normal']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('type') === 'normal' ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['normal'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Normal</div>
                </a>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow-sm px-4 py-3 mx-4 sm:mx-0">
                <form method="GET" action="{{ route('stock.groups') }}" class="flex gap-3">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Search group name...">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">Search</button>
                    @if(request('search'))
                        <a href="{{ route('stock.groups', request('type') ? ['type' => request('type')] : []) }}" class="px-4 py-2.5 text-gray-500 text-sm hover:text-gray-700">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($groups as $group)
                    <a href="{{ route('stock.groups.show', $group) }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-semibold text-gray-900 text-sm">{{ $group->name }}</div>
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium 
                                {{ $group->type === 'box' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $group->type === 'large' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $group->type === 'normal' ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ ucfirst($group->type) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>{{ $group->meters_count }} meters</span>
                            @if($group->stock_count > 0)
                                <span class="text-green-600">{{ $group->stock_count }} stock</span>
                            @endif
                            @if($group->assigned_count > 0)
                                <span class="text-blue-600">{{ $group->assigned_count }} assigned</span>
                            @endif
                            @if($group->installed_count > 0)
                                <span class="text-emerald-600">{{ $group->installed_count }} installed</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 py-8">No groups found.</div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden sm:block bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group Name</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assigned</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Installed</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Issue</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($groups as $group)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <a href="{{ route('stock.groups.show', $group) }}" class="font-medium text-indigo-600 hover:text-indigo-800 text-sm">
                                        {{ $group->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium 
                                        {{ $group->type === 'box' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $group->type === 'large' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $group->type === 'normal' ? 'bg-gray-100 text-gray-700' : '' }}">
                                        {{ ucfirst($group->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-semibold">{{ $group->meters_count }}</td>
                                <td class="px-4 py-3 text-center text-sm text-green-600">{{ $group->stock_count }}</td>
                                <td class="px-4 py-3 text-center text-sm text-blue-600">{{ $group->assigned_count }}</td>
                                <td class="px-4 py-3 text-center text-sm text-emerald-600">{{ $group->installed_count }}</td>
                                <td class="px-4 py-3 text-center text-sm text-red-600">{{ $group->issue_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('stock.groups.show', $group) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View</a>
                                        @if($group->meters_count === 0)
                                            <form method="POST" action="{{ route('stock.groups.destroy', $group) }}"
                                                  onsubmit="return confirm('Delete empty group?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">No groups found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-0">
                {{ $groups->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
