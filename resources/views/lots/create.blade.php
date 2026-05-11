<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('lots.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create New Lot') }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lots.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description / Label</label>
                            <input type="text" name="description" value="{{ old('description') }}" placeholder="e.g. Feb 2026 Procurement, Site A Batch"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Optional — helps identify this lot</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Import Date</label>
                            <input type="date" name="import_date" value="{{ old('import_date', now()->format('Y-m-d')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Date the meters were received/procured</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                            <strong>Lot Number</strong> will be auto-generated (e.g. LOT-001, LOT-002, ...)
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition">
                            Create Lot
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
