<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Lots') }}</h2>
            <a href="{{ route('lots.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                + New Lot
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="mb-4 mx-4 sm:mx-0 bg-white rounded-xl shadow-sm p-4">
                <form method="GET" action="{{ route('lots.index') }}" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="🔍 Search by lot number or description...">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('lots.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Unassigned meters notice --}}
            @if($unassignedCount > 0)
                <div class="mb-4 mx-4 sm:mx-0 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <span class="text-sm">⚠ <strong>{{ $unassignedCount }}</strong> meters have no lot assigned.</span>
                    <form method="POST" action="{{ route('lots.assign-existing') }}" class="flex items-center gap-2">
                        @csrf
                        <select name="lot_id" class="rounded-md border-gray-300 text-sm p-1.5 border" required>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}">{{ $lot->lot_number }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-amber-700">
                            Assign All
                        </button>
                    </form>
                </div>
            @endif

            {{-- Lots List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">

                    @if($lots->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <p class="text-lg mb-2">No lots created yet</p>
                            <p class="text-sm">Create your first lot to start organizing meters.</p>
                        </div>
                    @else
                        @php
                            $incompleteLots = $lots->where('is_complete', false);
                            $completeLots = $lots->where('is_complete', true);
                        @endphp

                        {{-- Incomplete lots (shown first) --}}
                        @if($incompleteLots->isNotEmpty())
                        <h3 class="text-sm font-semibold text-gray-600 mb-3">📋 Active Lots</h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
                            @foreach($incompleteLots as $lot)
                                @include('lots._card', ['lot' => $lot])
                            @endforeach
                        </div>
                        @endif

                        {{-- Complete lots (shown at bottom, faded) --}}
                        @if($completeLots->isNotEmpty())
                        <h3 class="text-sm font-semibold text-gray-400 mb-3 mt-6 border-t pt-4">✅ Completed Lots ({{ $completeLots->count() }})</h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 opacity-70">
                            @foreach($completeLots as $lot)
                                @include('lots._card', ['lot' => $lot])
                            @endforeach
                        </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
