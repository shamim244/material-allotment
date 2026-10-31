<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-xs text-indigo-600 hover:underline">&larr; Back to Platform Stats</a>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Contractor Directory</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage all partner agencies, quota limits, and access controls.</p>
            </div>
            <div>
                <a href="{{ route('super-admin.companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Onboard Contractor</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('super-admin.companies.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, NCC code, email…"
                       class="rounded-lg border-gray-200 text-sm focus:border-indigo-400 focus:ring-indigo-400 w-full sm:w-64">

                <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-sm font-medium rounded-lg transition">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('super-admin.companies.index') }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Clear</a>
                @endif
            </form>
        </div>

        <!-- Contractors Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Agency / Contractor</th>
                            <th class="py-3 px-4">NCC Code</th>
                            <th class="py-3 px-4">Contact Info</th>
                            <th class="py-3 px-4 text-center">Quota (Meters / Workers)</th>
                            <th class="py-3 px-4 text-center">Current Stock</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($companies as $company)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900">{{ $company->name }}</div>
                                <div class="text-xs text-gray-400">Onboarded {{ $company->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-indigo-600 font-semibold">
                                {{ $company->code ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-xs font-medium text-gray-800">{{ $company->contact_person }}</div>
                                <div class="text-xs text-gray-500">{{ $company->email }}</div>
                                @if($company->phone)<div class="text-[11px] text-gray-400">📞 {{ $company->phone }}</div>@endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="text-xs font-medium text-gray-800">{{ number_format($company->max_meters) }} meters</div>
                                <div class="text-[11px] text-gray-400">{{ $company->max_workers }} max workers</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ number_format($company->meters_count) }} meters
                                </span>
                                <div class="text-[11px] text-gray-400 mt-0.5">{{ $company->workers_count }} workers</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($company->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @elseif($company->status === 'trial')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Trial
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1">
                                <!-- Impersonate Contractor -->
                                <form method="POST" action="{{ route('super-admin.companies.impersonate', $company->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Log in as contractor admin">
                                        Login
                                    </button>
                                </form>

                                <!-- Edit -->
                                <a href="{{ route('super-admin.companies.edit', $company->id) }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                    Edit
                                </a>

                                <!-- Toggle Suspend -->
                                <form method="POST" action="{{ route('super-admin.companies.toggle', $company->id) }}" class="inline" onsubmit="return confirm('Toggle status for {{ $company->name }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $company->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition">
                                        {{ $company->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                No contractors matched your search.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($companies->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $companies->links() }}
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
