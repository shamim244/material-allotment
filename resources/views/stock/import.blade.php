<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Import') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ activeTab: 'box', selectedLot: '{{ $lots->first()?->id ?? '' }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    <!-- Lot Selector -->
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="text-sm font-semibold text-indigo-700">📦 Import into Lot:</label>
                            <select x-model="selectedLot"
                                class="flex-1 min-w-[200px] rounded-md border-indigo-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                @if($lots->isEmpty())
                                    <option value="">No lots — create one first</option>
                                @else
                                    @foreach($lots as $lot)
                                        <option value="{{ $lot->id }}">{{ $lot->lot_number }} {{ $lot->description ? '— ' . $lot->description : '' }} ({{ $lot->import_date?->format('d M Y') ?? 'No date' }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <a href="{{ route('lots.create') }}" class="text-xs bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 transition font-medium">
                                + New Lot
                            </a>
                        </div>
                        @if($lots->isEmpty())
                            <p class="text-xs text-amber-600 mt-2">⚠ Create a lot first to organize your imports.</p>
                        @endif
                    </div>

                    <!-- Mobile Friendly Tabs -->
                    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                        <button 
                            @click="activeTab = 'box'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'box', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'box' }"
                            class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                        >
                            Box Import
                        </button>
                        <button 
                            @click="activeTab = 'normal'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'normal', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'normal' }"
                            class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                        >
                            Normal Import
                        </button>
                        <button 
                            @click="activeTab = 'large'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'large', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'large' }"
                            class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                        >
                            Bulk Import
                        </button>
                    </div>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- =============== BOX IMPORT (2-Step) =============== -->
                    <div x-show="activeTab === 'box'" class="space-y-4"
                         x-data="{
                            boxNumber: '',
                            meters: [],
                            loading: false,
                            previewed: false,
                            error: '',
                            
                            async checkBox() {
                                if (!this.boxNumber.trim()) return;
                                this.loading = true;
                                this.error = '';
                                this.meters = [];
                                this.previewed = false;
                                
                                try {
                                    const res = await fetch('{{ route('stock.import.box.preview') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ start_meter_number: this.boxNumber.trim().toUpperCase() }),
                                    });
                                    
                                    const data = await res.json();
                                    
                                    if (!res.ok) {
                                        this.error = data.error || data.message || 'Invalid box number.';
                                        return;
                                    }
                                    
                                    this.meters = data.meters.map(m => ({
                                        ...m,
                                        selected: !m.exists,
                                    }));
                                    this.previewed = true;
                                    
                                    if (data.all_exist) {
                                        this.error = 'All meters in this box are already imported.';
                                    }
                                } catch (e) {
                                    this.error = 'Network error. Please try again.';
                                } finally {
                                    this.loading = false;
                                }
                            },
                            
                            get selectedCount() {
                                return this.meters.filter(m => m.selected && !m.exists).length;
                            },
                            
                            get existingCount() {
                                return this.meters.filter(m => m.exists).length;
                            },

                            reset() {
                                this.boxNumber = '';
                                this.meters = [];
                                this.previewed = false;
                                this.error = '';
                            }
                         }"
                    >
                        <div class="bg-blue-50 p-4 rounded-md mb-4">
                            <p class="text-sm text-blue-700">
                                <strong>Box Import:</strong> Enter the box start number (must end in 1 or 6). System will show all 5 meters — select which to import.
                            </p>
                        </div>

                        <!-- Step 1: Enter box number -->
                        <div>
                            <label for="box_number_input" class="block text-sm font-medium text-gray-700">Box Start Number</label>
                            <div class="mt-1 flex gap-2">
                                <input 
                                    type="text" 
                                    id="box_number_input"
                                    x-model="boxNumber"
                                    @keydown.enter.prevent="checkBox()"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3 border uppercase"
                                    placeholder="e.g. C1350151"
                                    :disabled="loading"
                                >
                                <button 
                                    @click="checkBox()"
                                    :disabled="loading || !boxNumber.trim()"
                                    class="px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2"
                                >
                                    <template x-if="loading">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </template>
                                    Check Box
                                </button>
                            </div>
                        </div>

                        <!-- Error -->
                        <template x-if="error">
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm" x-text="error"></div>
                        </template>

                        <!-- Step 2: Meter checkboxes -->
                        <template x-if="previewed && meters.length > 0">
                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b">
                                    <h4 class="text-sm font-semibold text-gray-700">
                                        Box: <span x-text="boxNumber.toUpperCase()" class="text-indigo-600"></span>
                                        <span class="ml-2 text-gray-400 font-normal">
                                            (<span x-text="existingCount"></span> already imported, 
                                             <span x-text="5 - existingCount"></span> available)
                                        </span>
                                    </h4>
                                </div>

                                <form method="POST" action="{{ route('stock.import.box') }}">
                                    @csrf
                                    <input type="hidden" name="lot_id" :value="selectedLot">
                                    <input type="hidden" name="box_start" :value="boxNumber.trim().toUpperCase()">
                                    
                                    <div class="divide-y">
                                        <template x-for="(meter, index) in meters" :key="index">
                                            <label 
                                                class="flex items-center px-4 py-3 transition"
                                                :class="meter.exists ? 'bg-gray-50 cursor-not-allowed' : 'hover:bg-indigo-50 cursor-pointer'"
                                            >
                                                <input 
                                                    type="checkbox"
                                                    name="selected_meters[]"
                                                    :value="meter.number"
                                                    :checked="meter.selected || meter.exists"
                                                    :disabled="meter.exists"
                                                    x-model="meter.selected"
                                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50"
                                                >
                                                <span class="ml-3 text-sm font-medium" 
                                                      :class="meter.exists ? 'text-gray-400 line-through' : 'text-gray-900'"
                                                      x-text="meter.number"></span>
                                                <template x-if="meter.exists">
                                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-medium">Already imported</span>
                                                </template>
                                            </label>
                                        </template>
                                    </div>

                                    <div class="px-4 py-3 bg-gray-50 border-t flex items-center justify-between gap-3">
                                        <button type="button" @click="reset()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition">
                                            ← Reset
                                        </button>
                                        <button 
                                            type="submit"
                                            :disabled="selectedCount === 0"
                                            class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                        >
                                            Import <span x-text="selectedCount"></span> Meter(s)
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>

                    <!-- =============== NORMAL IMPORT =============== -->
                    <div x-show="activeTab === 'normal'" class="space-y-4" style="display: none;">
                        <div class="bg-gray-50 p-4 rounded-md mb-4">
                            <p class="text-sm text-gray-700">
                                <strong>Single/Multi Input:</strong> Enter comma-separated meter numbers.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('stock.import.normal') }}">
                            @csrf
                            <input type="hidden" name="lot_id" :value="selectedLot">
                            <div>
                                <label for="meter_numbers" class="block text-sm font-medium text-gray-700">Meter Numbers</label>
                                <textarea 
                                    name="meter_numbers"
                                    id="meter_numbers" 
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                    placeholder="C1350140, C1350143, C170195"
                                    required
                                ></textarea>
                            </div>
                            <div class="mt-4">
                                <button 
                                    type="submit" 
                                    class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Import Meters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- =============== BULK IMPORT =============== -->
                    <div x-show="activeTab === 'large'" class="space-y-4" style="display: none;">
                        <div class="bg-green-50 p-4 rounded-md mb-4">
                            <p class="text-sm text-green-700">
                                <strong>Range Mode:</strong> Import bulk meters. System will auto-group them into aligned boxes of 5 (starting at numbers ending in 1 or 6).
                            </p>
                        </div>
                        <form method="POST" action="{{ route('stock.import.large') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="lot_id" :value="selectedLot">
                            <div>
                                <label for="start_meter" class="block text-sm font-medium text-gray-700">Start Meter</label>
                                <input 
                                    type="text" 
                                    name="start_meter"
                                    id="start_meter" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3 border"
                                    placeholder="C1350150"
                                    required
                                >
                            </div>
                            <div>
                                <label for="end_meter" class="block text-sm font-medium text-gray-700">End Meter</label>
                                <input 
                                    type="text" 
                                    name="end_meter"
                                    id="end_meter" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3 border"
                                    placeholder="C1350240"
                                    required
                                >
                            </div>
                            <div class="mt-4">
                                <button 
                                    type="submit" 
                                    class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Import Range
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
