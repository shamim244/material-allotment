<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('lots.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $lot->lot_number }} {{ $lot->description ? '— ' . $lot->description : '' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Lot Info + Edit --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mx-4 sm:mx-0">
                {{-- Info --}}
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Lot Info</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Lot Number</span>
                            <span class="font-bold text-indigo-700">{{ $lot->lot_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Import Date</span>
                            <span>{{ $lot->import_date?->format('d M Y') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created</span>
                            <span>{{ $lot->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Edit --}}
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Edit Lot</h3>
                    <form method="POST" action="{{ route('lots.update', $lot) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <input type="text" name="description" value="{{ $lot->description }}" placeholder="Description"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                        <input type="date" name="import_date" value="{{ $lot->import_date?->format('Y-m-d') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Save</button>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mx-4 sm:mx-0">
                @foreach([
                    ['Total', $stats['total'], 'text-gray-700', 'bg-gray-50'],
                    ['Stock', $stats['stock'], 'text-green-700', 'bg-green-50'],
                    ['Assigned', $stats['assigned'], 'text-blue-700', 'bg-blue-50'],
                    ['Installed', $stats['installed'], 'text-emerald-700', 'bg-emerald-50'],
                    ['Issues', $stats['issue'], 'text-red-700', 'bg-red-50'],
                ] as [$label, $count, $textColor, $bgColor])
                    <div class="{{ $bgColor }} rounded-lg p-3 text-center shadow-sm">
                        <div class="text-xs uppercase text-gray-500">{{ $label }}</div>
                        <div class="text-xl font-bold {{ $textColor }}">{{ $count }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Groups in this lot --}}
            @if($groups->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Groups in this Lot</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($groups as $group)
                            <a href="{{ route('stock.groups.show', $group) }}"
                               class="flex items-center justify-between border rounded-lg p-3 hover:shadow-md transition hover:border-indigo-300">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $group->name ?? 'Unnamed' }}</span>
                                    <span class="ml-2 text-xs text-gray-400">{{ $group->type }}</span>
                                </div>
                                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 text-xs rounded-full font-semibold">{{ $group->meters_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Loose meters (no group) --}}
            @if($looseMeters->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Loose Meters (No Group)</h3>
                    <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach($looseMeters as $meter)
                            <a href="{{ route('stock.show', $meter) }}"
                               class="border rounded-lg p-3 text-center hover:shadow-md transition hover:border-indigo-300">
                                <div class="font-mono font-bold text-indigo-700 text-sm">{{ $meter->meter_number }}</div>
                                @php
                                    $statusColors = [
                                        'stock' => 'bg-green-100 text-green-700',
                                        'assigned' => 'bg-blue-100 text-blue-700',
                                        'installed' => 'bg-emerald-100 text-emerald-700',
                                        'issue' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($meter->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- All Meters — Bulk actions with checkboxes --}}
            @php
                $allMeters = $lot->meters()->orderBy('meter_number')->get();
                $stockMeters = $allMeters->where('status', 'stock');
            @endphp
            @if($allMeters->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mx-4 sm:mx-0"
                 x-data="{
                    selected: [],
                    filterStatus: '',
                    toggleAll(ids) {
                        if (this.selected.length === ids.length) {
                            this.selected = [];
                        } else {
                            this.selected = [...ids];
                        }
                    }
                 }">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">
                            All Meters in Lot <span class="text-indigo-600">({{ $allMeters->count() }})</span>
                            <span class="ml-2 text-xs text-green-600 font-normal">{{ $stockMeters->count() }} in stock</span>
                        </h3>
                        <div class="flex items-center gap-2">
                            <select x-model="filterStatus" class="rounded-md border-gray-300 shadow-sm text-xs p-2 border">
                                <option value="">All Status</option>
                                <option value="stock">Stock</option>
                                <option value="assigned">Assigned</option>
                                <option value="installed">Installed</option>
                                <option value="issue">Issue</option>
                            </select>
                            @if($stockMeters->isNotEmpty())
                                <button type="button"
                                    @click="toggleAll([{{ $stockMeters->pluck('id')->join(',') }}])"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-md transition">
                                    <span x-text="selected.length === {{ $stockMeters->count() }} ? 'Deselect All Stock' : 'Select All Stock'"></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-xs">
                            @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                        </div>
                    @endif

                    {{-- Bulk delete form --}}
                    <form method="POST" action="{{ route('stock.bulkDelete') }}"
                          x-show="selected.length > 0"
                          x-cloak
                          onsubmit="return confirm('Delete ' + document.querySelectorAll('input[name=\'meter_ids[]\']:checked').length + ' stock meter(s)? This cannot be undone!')"
                          class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex flex-wrap items-center gap-3">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="meter_ids[]" :value="id">
                        </template>
                        <span class="text-sm text-red-700 font-medium">
                            🗑 <span x-text="selected.length"></span> stock meter(s) selected
                        </span>
                        <button type="submit" class="px-4 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition">
                            Delete Selected
                        </button>
                        <button type="button" @click="selected = []" class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800 transition">
                            Cancel
                        </button>
                    </form>

                    {{-- Meters grid --}}
                    <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach($allMeters as $meter)
                            <div x-show="!filterStatus || filterStatus === '{{ $meter->status }}'"
                                 class="border rounded-lg p-3 text-center transition {{ $meter->status === 'stock' ? 'hover:border-red-300 cursor-pointer' : '' }}"
                                 :class="selected.includes({{ $meter->id }}) ? 'border-red-400 bg-red-50' : 'hover:border-indigo-300'"
                                 @if($meter->status === 'stock')
                                    @click="selected.includes({{ $meter->id }}) ? selected = selected.filter(i => i !== {{ $meter->id }}) : selected.push({{ $meter->id }})"
                                 @endif
                            >
                                @if($meter->status === 'stock')
                                    <div class="flex justify-end -mb-2 -mt-1">
                                        <input type="checkbox" :checked="selected.includes({{ $meter->id }})"
                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4"
                                            @click.stop>
                                    </div>
                                @endif
                                <a href="{{ route('stock.show', $meter) }}" @click.stop class="font-mono font-bold text-indigo-700 text-sm hover:underline">{{ $meter->meter_number }}</a>
                                @php
                                    $statusColors = [
                                        'stock' => 'bg-green-100 text-green-700',
                                        'assigned' => 'bg-blue-100 text-blue-700',
                                        'installed' => 'bg-emerald-100 text-emerald-700',
                                        'issue' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <div class="mt-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($meter->status) }}
                                    </span>
                                </div>
                                @if($meter->group)
                                    <a href="{{ route('stock.groups.show', $meter->group) }}" @click.stop class="text-xs text-indigo-500 hover:underline mt-1 truncate block">{{ $meter->group->name }}</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
