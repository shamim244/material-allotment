<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition shadow-sm">
                + New User
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-4 px-4 sm:px-0">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Users</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Active</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-amber-600">{{ $stats['inactive'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Inactive</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['deleted'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Deleted</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['contractors'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Contractors</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['workers'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Workers</div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search by name or email...">
                        <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                        <select name="role" class="rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="contractor" {{ request('role') === 'contractor' ? 'selected' : '' }}>Contractor</option>
                            <option value="worker" {{ request('role') === 'worker' ? 'selected' : '' }}>Worker</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'role']))
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition text-center">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- User List -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Users ({{ $users->total() }})</h3>

                    @if($users->isEmpty())
                        <p class="text-gray-500 text-sm">No users found.</p>
                    @else
                        <!-- Mobile Cards -->
                        <div class="sm:hidden space-y-3">
                            @foreach($users as $user)
                            <div class="border rounded-lg p-4 {{ $user->trashed() ? 'bg-red-50 opacity-60' : (!$user->is_active ? 'bg-gray-50 opacity-75' : '') }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $user->role === 'contractor' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                        @if($user->trashed())
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium bg-gray-200 text-gray-600">Deleted</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 mb-3">Joined {{ $user->created_at->format('d M Y') }}</div>
                                @if($user->id !== Auth::id())
                                <div class="flex flex-wrap gap-2">
                                    @if($user->trashed())
                                        <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200 transition">
                                                Restore
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md transition
                                                {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.impersonate', $user->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                                                Login As
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user? Data will be preserved.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                @else
                                <span class="text-xs text-gray-400 italic">Current account</span>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Joined</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr class="{{ $user->trashed() ? 'bg-red-50 opacity-60' : (!$user->is_active ? 'bg-gray-50 opacity-75' : '') }}">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $user->role === 'contractor' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($user->trashed())
                                                <span class="px-2 py-0.5 text-xs rounded-full font-medium bg-gray-200 text-gray-600">Deleted</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                                            {{ $user->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($user->id !== Auth::id())
                                            <div class="flex justify-center gap-2">
                                                @if($user->trashed())
                                                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200 transition">
                                                            Restore
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium rounded-md transition
                                                            {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.users.impersonate', $user->id) }}">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                                                            Login As
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user? Data will be preserved.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            @else
                                            <span class="text-xs text-gray-400 italic">You</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
