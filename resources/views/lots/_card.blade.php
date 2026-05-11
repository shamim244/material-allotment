<a href="{{ route('lots.show', $lot) }}"
   class="block border rounded-lg p-5 hover:shadow-lg transition {{ $lot->is_complete ? 'border-green-200 bg-green-50/30' : 'hover:border-indigo-300' }}">
    <div class="flex items-center justify-between mb-2">
        <span class="text-lg font-bold {{ $lot->is_complete ? 'text-green-700' : 'text-indigo-700' }}">{{ $lot->lot_number }}</span>
        <div class="flex items-center gap-2">
            @if($lot->is_complete)
                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">✅ Complete</span>
            @endif
            <span class="text-xs text-gray-400">{{ $lot->import_date?->format('d M Y') ?? '—' }}</span>
        </div>
    </div>

    @if($lot->description)
        <p class="text-sm text-gray-600 mb-3">{{ $lot->description }}</p>
    @endif

    {{-- Completion progress bar --}}
    <div class="mb-3">
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="text-gray-500">Progress</span>
            <span class="font-semibold {{ $lot->is_complete ? 'text-green-600' : ($lot->completion_pct > 50 ? 'text-yellow-600' : 'text-gray-600') }}">
                {{ $lot->completion_pct }}%
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all {{ $lot->is_complete ? 'bg-green-500' : ($lot->completion_pct > 50 ? 'bg-yellow-500' : 'bg-indigo-500') }}"
                 style="width: {{ $lot->completion_pct }}%"></div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-2 text-center">
        <div class="bg-gray-50 rounded p-2">
            <div class="text-xs text-gray-500">Total</div>
            <div class="font-bold text-gray-700">{{ $lot->meters_count }}</div>
        </div>
        <div class="bg-green-50 rounded p-2">
            <div class="text-xs text-green-600">Stock</div>
            <div class="font-bold text-green-700">{{ $lot->stock_count }}</div>
        </div>
        <div class="bg-blue-50 rounded p-2">
            <div class="text-xs text-blue-600">Assigned</div>
            <div class="font-bold text-blue-700">{{ $lot->assigned_count }}</div>
        </div>
        <div class="bg-emerald-50 rounded p-2">
            <div class="text-xs text-emerald-600">Installed</div>
            <div class="font-bold text-emerald-700">{{ $lot->installed_count }}</div>
        </div>
    </div>

    <div class="mt-3 text-xs text-gray-400">
        {{ $lot->groups_count }} group(s)
    </div>
</a>
