<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">👑 Super Admin Master</span>
                    <span class="text-xs text-gray-400">SaaS Platform Control</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Platform Overview</h1>
                <p class="text-xs text-gray-500 mt-0.5">Manage all NCC-partnered contractors, inventory limits, and subscriptions.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('super-admin.companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Onboard New Contractor</span>
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Contractors -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Contractors</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_companies']) }}</div>
                    <div class="text-[11px] text-emerald-600 font-medium">{{ $stats['active_companies'] }} Active • {{ $stats['suspended_companies'] }} Suspended</div>
                </div>
            </div>

            <!-- Total Meters Across Platform -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Platform Meters</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_meters']) }}</div>
                    <div class="text-[11px] text-gray-500 font-medium">{{ number_format($stats['in_stock_meters']) }} In Stock</div>
                </div>
            </div>

            <!-- Installed Meters -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Installed Meters</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['installed_meters']) }}</div>
                    <div class="text-[11px] text-gray-500 font-medium">Completed in Field</div>
                </div>
            </div>

            <!-- Total Field Workers -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Active Workers</div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_workers']) }}</div>
                    <div class="text-[11px] text-gray-500 font-medium">{{ $stats['total_contractors'] }} Contractor Admins</div>
                </div>
            </div>
        </div>

        <!-- Contractors Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Contractor Agencies</h2>
                    <p class="text-xs text-gray-500">All registered contractor workspaces operating under NCC.</p>
                </div>
                <a href="{{ route('super-admin.companies.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    View All Contractors &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Contractor / Agency</th>
                            <th class="py-3 px-4">NCC Code</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4 text-center">Meters</th>
                            <th class="py-3 px-4 text-center">Workers</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($companies as $company)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900">{{ $company->name }}</div>
                                <div class="text-xs text-gray-400">Created {{ $company->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-indigo-600 font-semibold">
                                {{ $company->code ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-xs font-medium text-gray-800">{{ $company->contact_person ?: '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $company->email ?: $company->phone }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ number_format($company->meters_count) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ $company->workers_count }}
                                </span>
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
                            <td class="py-3.5 px-4 text-right space-x-1.5">
                                <!-- Impersonate Contractor -->
                                <form method="POST" action="{{ route('super-admin.companies.impersonate', $company->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Log in as contractor admin">
                                        Login as Agency
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
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                No contractors onboarded yet. Click "Onboard New Contractor" to add one.
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
