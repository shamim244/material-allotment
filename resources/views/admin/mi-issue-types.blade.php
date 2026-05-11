<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('MI Issue Types') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
            @endif

            <!-- Add New Issue Type -->
            <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Add New Issue Type</h3>
                <form method="POST" action="{{ route('admin.mi-issue-types.store') }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="name" required placeholder="e.g. QR Code Issue, Meter Damaged..."
                        class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('name') }}">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition whitespace-nowrap">
                        + Add
                    </button>
                </form>
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Issue Types List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Issue Types ({{ $issueTypes->count() }})</h3>
                        <p class="text-xs text-gray-400">Workers see active types when reporting MI issues</p>
                    </div>

                    @if($issueTypes->isEmpty())
                        <div class="text-center py-8 text-gray-400 text-sm">
                            No issue types yet. Add one above.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($issueTypes as $type)
                                <div class="flex items-center justify-between p-3 rounded-lg border {{ $type->is_active ? 'border-gray-200 bg-white' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $type->is_active ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-200 text-gray-400' }}">
                                            {{ $type->sort_order }}
                                        </span>
                                        <div>
                                            <span class="text-sm font-medium {{ $type->is_active ? 'text-gray-900' : 'text-gray-400 line-through' }}">{{ $type->name }}</span>
                                            @if($type->activations_count > 0)
                                                <span class="text-xs text-gray-400 ml-2">Used {{ $type->activations_count }}×</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <!-- Toggle Active -->
                                        <form method="POST" action="{{ route('admin.mi-issue-types.toggle', $type) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 text-xs rounded-md font-medium transition
                                                {{ $type->is_active 
                                                    ? 'bg-green-100 text-green-700 hover:bg-green-200' 
                                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                                {{ $type->is_active ? '✓ Active' : '✗ Inactive' }}
                                            </button>
                                        </form>

                                        <!-- Delete (only if unused) -->
                                        @if($type->activations_count === 0)
                                            <form method="POST" action="{{ route('admin.mi-issue-types.destroy', $type) }}"
                                                  onsubmit="return confirm('Delete issue type &quot;{{ $type->name }}&quot;?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-2 py-1.5 text-xs text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition">
                                                    🗑
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-xs text-gray-400 mt-4">
                            💡 "Other" option is always available — workers can type a custom reason when none of these fit.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
