<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('MI - Meter Activation') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="mb-6 px-4 sm:px-0 grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                    <div class="text-gray-500 text-xs uppercase">Total</div>
                    <div class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-yellow-50 shadow-sm rounded-lg p-4 text-center border border-yellow-200">
                    <div class="text-yellow-600 text-xs uppercase">Pending</div>
                    <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-red-50 shadow-sm rounded-lg p-4 text-center border border-red-200">
                    <div class="text-red-600 text-xs uppercase">Issue</div>
                    <div class="text-2xl font-bold text-red-700">{{ $stats['issue'] }}</div>
                </div>
                <div class="bg-green-50 shadow-sm rounded-lg p-4 text-center border border-green-200">
                    <div class="text-green-600 text-xs uppercase">Complete</div>
                    <div class="text-2xl font-bold text-green-700">{{ $stats['complete'] }}</div>
                </div>
                <div class="bg-purple-50 shadow-sm rounded-lg p-4 text-center border border-purple-200">
                    <div class="text-purple-600 text-xs uppercase">Job Not Found</div>
                    <div class="text-2xl font-bold text-purple-700">{{ $stats['job_not_found'] }}</div>
                </div>
            </div>

            <!-- Search + Filters -->
            <div class="mb-4 px-4 sm:px-0">
                <form method="GET" action="{{ route('mi.index') }}" class="space-y-3">
                    
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="🔍 Search by meter number..."
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm p-3 pl-4 border focus:border-indigo-500 focus:ring-indigo-500">
                        @if(request('search'))
                            <a href="{{ route('mi.index') }}" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">✕</a>
                        @endif
                    </div>

                    <!-- Filter Row -->
                    <div class="flex flex-wrap gap-3 items-end">
                        @if(Auth::user()->role === 'contractor' && $workers->isNotEmpty())
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Worker</label>
                            <select name="worker_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Workers</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="issue" {{ request('status') === 'issue' ? 'selected' : '' }}>Issue</option>
                                <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="job_not_found" {{ request('status') === 'job_not_found' ? 'selected' : '' }}>Job Not Found</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select name="install_type" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" {{ request('install_type') === 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="nsc" {{ request('install_type') === 'nsc' ? 'selected' : '' }}>🆕 NSC</option>
                                <option value="replacement" {{ request('install_type') === 'replacement' ? 'selected' : '' }}>🔄 Replacement</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'worker_id', 'status', 'install_type']))
                            <a href="{{ route('mi.index') }}" class="text-xs text-gray-500 hover:text-indigo-600 py-2">Clear All</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- MI List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($activations->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            @if(request('search'))
                                No MI records found for "{{ request('search') }}".
                            @else
                                No MI records found.
                            @endif
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($activations as $activation)
                                <a href="{{ route('mi.show', $activation->id) }}" 
                                   class="flex flex-col border rounded-xl p-4 hover:shadow-lg transition hover:border-indigo-300 group">
                                    {{-- Row 1: Meter # + Status Badge --}}
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-bold text-lg text-indigo-700 group-hover:text-indigo-800">{{ $activation->meter->meter_number }}</span>
                                        @php
                                            $statusColors = [
                                                'pending'       => 'bg-yellow-100 text-yellow-800',
                                                'issue'         => 'bg-red-100 text-red-800',
                                                'complete'      => 'bg-green-100 text-green-800',
                                                'job_not_found' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full font-medium {{ $statusColors[$activation->mi_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $activation->mi_status === 'job_not_found' ? 'Job Not Found' : ucfirst($activation->mi_status) }}
                                        </span>
                                    </div>

                                    {{-- Row 2: Type Badge + Issue --}}
                                    <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                        @if($activation->meter->installationDetail)
                                            @if($activation->meter->installationDetail->install_type === 'replacement')
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 font-medium">🔄 Replacement</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 font-medium">🆕 NSC</span>
                                            @endif
                                        @endif

                                        @if($activation->mi_status === 'issue' && ($activation->issueType || $activation->issue_detail))
                                            <span class="text-xs text-red-600 font-medium">
                                                ⚠ {{ $activation->issueType ? $activation->issueType->name : 'Other: ' . $activation->issue_detail }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Middle: Images + Remark --}}
                                    @if($activation->images->count() > 0 || $activation->remark)
                                    <div class="mb-2 space-y-1">
                                        @if($activation->images->count() > 0)
                                        <div class="flex items-center text-xs text-indigo-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>
                                            {{ $activation->images->count() }} image(s)
                                        </div>
                                        @endif
                                        @if($activation->remark)
                                        <div class="text-xs text-gray-500 truncate">{{ $activation->remark }}</div>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Footer: Updated (left) + Worker (right) --}}
                                    <div class="mt-auto pt-2 border-t border-gray-100 flex items-center justify-between">
                                        <span class="text-xs text-gray-400">{{ $activation->updated_at->diffForHumans() }}</span>
                                        @if(Auth::user()->role === 'contractor')
                                            <span class="text-xs text-gray-500">
                                                👷 <span class="text-indigo-600 hover:underline" @click.stop>{{ $activation->worker->name }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $activations->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
