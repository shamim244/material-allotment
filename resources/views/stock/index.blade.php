<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stock Management') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('stock.groups') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    Groups
                </a>
                <a href="{{ route('stock.import') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                    + Import
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        selectedIds: [],
        selectAll: false,
        pageIds: @js($meters->pluck('id')->toArray()),
        toggleAll() {
            if (this.selectAll) { this.selectedIds = [...this.pageIds]; }
            else { this.selectedIds = []; }
        },
        toggleOne(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx > -1) { this.selectedIds.splice(idx, 1); }
            else { this.selectedIds.push(id); }
            this.selectAll = this.selectedIds.length === this.pageIds.length;
        },
        isSelected(id) { return this.selectedIds.includes(id); },
        submitBulk(action, extraData = {}) {
            const form = this.$refs.bulkForm;
            form.action = action;
            form.querySelectorAll('.bulk-extra').forEach(el => el.remove());
            this.selectedIds.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'meter_ids[]'; inp.value = id; inp.className = 'bulk-extra';
                form.appendChild(inp);
            });
            for (const [k, v] of Object.entries(extraData)) {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = k; inp.value = v; inp.className = 'bulk-extra';
                form.appendChild(inp);
            }
            form.submit();
        }
    }">
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 px-4 sm:px-0">
                <a href="{{ route('stock.index') }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ !request('status') ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total</div>
                </a>
                <a href="{{ route('stock.index', ['status' => 'stock']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') === 'stock' ? 'ring-2 ring-green-500' : '' }}">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['stock'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">In Stock</div>
                </a>
                <a href="{{ route('stock.index', ['status' => 'assigned']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') === 'assigned' ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['assigned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Assigned</div>
                </a>
                <a href="{{ route('stock.index', ['status' => 'installed']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') === 'installed' ? 'ring-2 ring-emerald-500' : '' }}">
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['installed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Installed</div>
                </a>
                <a href="{{ route('stock.index', ['status' => 'issue']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') === 'issue' ? 'ring-2 ring-red-500' : '' }}">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['issue'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Issues</div>
                </a>
                <a href="{{ route('stock.index', ['status' => 'returned']) }}" class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') === 'returned' ? 'ring-2 ring-gray-500' : '' }}">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['returned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Returned</div>
                </a>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white rounded-lg shadow-sm px-4 py-3 mx-4 sm:mx-0">
                <form method="GET" action="{{ route('stock.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <!-- Keep existing filters -->
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search meter number...">
                    </div>

                    <select name="group" class="rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Groups</option>
                        <option value="ungrouped" {{ request('group') === 'ungrouped' ? 'selected' : '' }}>Ungrouped</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} ({{ $group->meters_count }})
                            </option>
                        @endforeach
                    </select>

                    <select name="lot" class="rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Lots</option>
                        <option value="unassigned" {{ request('lot') === 'unassigned' ? 'selected' : '' }}>No Lot</option>
                        @foreach($lots as $lot)
                            <option value="{{ $lot->id }}" {{ request('lot') == $lot->id ? 'selected' : '' }}>
                                {{ $lot->lot_number }} ({{ $lot->meters_count }})
                            </option>
                        @endforeach
                    </select>

                    <select name="sort" class="rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="meter_asc" {{ request('sort') === 'meter_asc' ? 'selected' : '' }}>Meter ↑</option>
                        <option value="meter_desc" {{ request('sort') === 'meter_desc' ? 'selected' : '' }}>Meter ↓</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                        Search
                    </button>

                    @if(request('search') || request('group') || request('sort'))
                        <a href="{{ route('stock.index', request('status') ? ['status' => request('status')] : []) }}" class="px-4 py-2.5 text-gray-500 text-sm hover:text-gray-700 text-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Results Count -->
            <div class="px-4 sm:px-0 text-sm text-gray-500">
                Showing {{ $meters->firstItem() ?? 0 }}–{{ $meters->lastItem() ?? 0 }} of {{ $meters->total() }} meters
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden space-y-3 px-4">
                @forelse($meters as $meter)
                    <div class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition"
                         :class="{ 'ring-2 ring-indigo-400 bg-indigo-50/30': isSelected({{ $meter->id }}) }">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                   :checked="isSelected({{ $meter->id }})" @change="toggleOne({{ $meter->id }})">
                            <a href="{{ route('stock.show', $meter) }}" class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-indigo-700 text-lg">{{ $meter->meter_number }}</div>
                            @php
                                $statusColors = [
                                    'stock' => 'bg-green-100 text-green-700',
                                    'assigned' => 'bg-blue-100 text-blue-700',
                                    'installed' => 'bg-emerald-100 text-emerald-700',
                                    'issue' => 'bg-red-100 text-red-700',
                                    'returned' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($meter->status) }}
                            </span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $meter->group ? $meter->group->name : 'Ungrouped' }}</span>
                            @if($meter->lot)
                                <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded text-xs font-medium">{{ $meter->lot->lot_number }}</span>
                            @endif
                            @if($meter->currentWorker)
                                <span>→ <a href="{{ route('workers.show', $meter->currentWorker) }}" class="hover:underline" @click.stop>{{ $meter->currentWorker->name }}</a></span>
                            @endif
                        </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">No meters found.</div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       x-model="selectAll" @change="toggleAll()">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Number</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imported</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($meters as $meter)
                            <tr class="hover:bg-gray-50 transition" :class="{ 'bg-indigo-50/50': isSelected({{ $meter->id }}) }">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           :checked="isSelected({{ $meter->id }})" @change="toggleOne({{ $meter->id }})">
                                </td>
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
                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($meter->lot)
                                        <a href="{{ route('lots.show', $meter->lot) }}" class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium hover:bg-indigo-100">{{ $meter->lot->lot_number }}</a>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @if($meter->group)
                                        <a href="{{ route('stock.groups.show', $meter->group) }}" class="hover:text-indigo-600">{{ $meter->group->name }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($meter->currentWorker)
                                        <a href="{{ route('workers.show', $meter->currentWorker) }}" class="text-indigo-600 hover:underline">{{ $meter->currentWorker->name }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">
                                    {{ $meter->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('stock.show', $meter) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    No meters found. <a href="{{ route('stock.import') }}" class="text-indigo-600 hover:underline">Import stock</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-0">
                {{ $meters->links() }}
            </div>

            <!-- Bulk Action Hidden Form -->
            <form x-ref="bulkForm" method="POST" style="display:none">
                @csrf
            </form>

            <!-- Floating Bulk Action Bar -->
            <div x-show="selectedIds.length > 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
                 x-cloak class="fixed bottom-0 inset-x-0 z-50 pb-4 px-4">
                <div class="max-w-5xl mx-auto bg-gray-900 text-white rounded-2xl shadow-2xl px-5 py-3 flex flex-wrap items-center gap-3">
                    <!-- Count -->
                    <div class="flex items-center gap-2">
                        <span class="bg-indigo-500 text-white text-xs font-bold px-2.5 py-1 rounded-full" x-text="selectedIds.length"></span>
                        <span class="text-sm text-gray-300">selected</span>
                    </div>

                    <div class="w-px h-6 bg-gray-700"></div>

                    <!-- Change Status -->
                    <div x-data="{ statusOpen: false }" class="relative">
                        <button @click="statusOpen = !statusOpen" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition">
                            📋 Status <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="statusOpen" @click.away="statusOpen = false" x-transition x-cloak
                             class="absolute bottom-full mb-2 left-0 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-200 py-1 w-40 z-50">
                            @foreach(['stock', 'assigned', 'installed', 'issue', 'returned'] as $s)
                                <button @click="submitBulk('{{ route('stock.bulkStatus') }}', { status: '{{ $s }}' }); statusOpen = false"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition">{{ ucfirst($s) }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Assign to Worker -->
                    <div x-data="{ assignOpen: false }" class="relative">
                        <button @click="assignOpen = !assignOpen" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition">
                            👷 Assign <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="assignOpen" @click.away="assignOpen = false" x-transition x-cloak
                             class="absolute bottom-full mb-2 left-0 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-200 py-1 w-48 max-h-60 overflow-y-auto z-50">
                            @foreach($workers as $worker)
                                <button @click="submitBulk('{{ route('stock.bulkAssign') }}', { worker_id: '{{ $worker->id }}' }); assignOpen = false"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition">{{ $worker->name }}</button>
                            @endforeach
                            @if($workers->isEmpty())
                                <div class="px-4 py-2 text-sm text-gray-400">No workers</div>
                            @endif
                        </div>
                    </div>

                    <!-- Export -->
                    <button @click="submitBulk('{{ route('stock.bulkExport') }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 rounded-lg text-sm transition">
                        📥 Export CSV
                    </button>

                    <div class="flex-1"></div>

                    <!-- Deselect -->
                    <button @click="selectedIds = []; selectAll = false" class="px-3 py-1.5 text-gray-400 hover:text-white text-sm transition">
                        ✕ Clear
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
