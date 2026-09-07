<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Auth::user()->role === 'contractor' ? __('Manage Meters') : __('My Meters') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Admin Worker Filter + Search -->
            @if(Auth::user()->role === 'contractor')
                <div class="mb-4 px-4 sm:px-0">
                    <form method="GET" action="{{ route('worker.dashboard') }}" class="flex flex-col sm:flex-row gap-2">
                        <select name="worker_id" class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All (Workers + Unassigned)</option>
                            <option value="unassigned" {{ request('worker_id') === 'unassigned' ? 'selected' : '' }}>📦 Unassigned (Stock)</option>
                            @foreach($workers as $w)
                                <option value="{{ $w->id }}" {{ request('worker_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="🔍 Search meter number...">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Filter</button>
                        @if(request()->hasAny(['worker_id', 'search', 'status']))
                            <a href="{{ route('worker.dashboard') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">Clear</a>
                        @endif
                    </form>
                </div>
            @else
                {{-- Worker search --}}
                <div class="mb-4 px-4 sm:px-0">
                    <form method="GET" action="{{ route('worker.dashboard') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="🔍 Search meter number...">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Search</button>
                        @if(request('search'))
                            <a href="{{ route('worker.dashboard', ['status' => request('status')]) }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition text-center">Clear</a>
                        @endif
                    </form>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="mb-4 px-4 sm:px-0 grid grid-cols-2 sm:grid-cols-{{ Auth::user()->role === 'contractor' ? '5' : '4' }} gap-3">
                @php
                    $currentStatus = request('status', 'all');
                    $filterBase = array_filter(['worker_id' => request('worker_id'), 'search' => request('search')]);
                @endphp
                <a href="{{ route('worker.dashboard', array_merge($filterBase, ['status' => 'assigned'])) }}"
                   class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-blue-500 hover:shadow-md transition {{ $currentStatus === 'assigned' ? 'ring-2 ring-blue-400' : '' }}">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['assigned'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Assigned</div>
                </a>
                <a href="{{ route('worker.dashboard', array_merge($filterBase, ['status' => 'all'])) }}"
                   class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-green-500 hover:shadow-md transition {{ $currentStatus === 'all' ? 'ring-2 ring-green-400' : '' }}">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['installed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Installed</div>
                </a>
                <a href="{{ route('worker.dashboard', array_merge($filterBase, ['status' => 'issue'])) }}"
                   class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-red-500 hover:shadow-md transition {{ $currentStatus === 'issue' ? 'ring-2 ring-red-400' : '' }}">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['issues'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Issues</div>
                </a>
                <a href="{{ route('worker.dashboard', array_merge($filterBase, ['status' => 'return_pending'])) }}"
                   class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-orange-500 hover:shadow-md transition {{ $currentStatus === 'return_pending' ? 'ring-2 ring-orange-400' : '' }}">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['return_pending'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Return Pending</div>
                </a>
                @if(Auth::user()->role === 'contractor')
                <a href="{{ route('worker.dashboard', array_merge($filterBase, ['status' => 'stock'])) }}"
                   class="bg-white rounded-xl shadow-sm p-3 text-center border-t-4 border-gray-400 hover:shadow-md transition {{ $currentStatus === 'stock' ? 'ring-2 ring-gray-400' : '' }}">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['stock'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Stock</div>
                </a>
                @endif
            </div>

            <!-- Meters List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 space-y-4">
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($assignedMeters->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            {{ Auth::user()->role === 'contractor' ? 'No meters match filter.' : 'No meters currently assigned to you.' }}
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($assignedMeters as $meter)
                                <div class="border rounded-lg p-4 flex flex-col justify-between hover:shadow-md transition"
                                     x-data="{ 
                                        showInstallModal: false, 
                                        fetchingLocation: false,
                                        latitude: '',
                                        longitude: '',
                                        locationError: '',
                                        fetchLocation() {
                                            if (!window.isSecureContext) {
                                                this.locationError = 'Location requires HTTPS. Please access this site via https:// or contact admin.';
                                                return;
                                            }
                                            if (!navigator.geolocation) {
                                                this.locationError = 'Geolocation not supported by your browser.';
                                                return;
                                            }
                                            this.fetchingLocation = true;
                                            this.locationError = '';
                                            navigator.geolocation.getCurrentPosition(
                                                (pos) => {
                                                    this.latitude = pos.coords.latitude.toFixed(7);
                                                    this.longitude = pos.coords.longitude.toFixed(7);
                                                    this.fetchingLocation = false;
                                                },
                                                (err) => {
                                                    if (err.code === 1) {
                                                        this.locationError = 'Location permission denied. Please allow location access in your browser settings.';
                                                    } else if (err.code === 2) {
                                                        this.locationError = 'Location unavailable. Please check your GPS/network.';
                                                    } else if (err.code === 3) {
                                                        this.locationError = 'Location request timed out. Please try again.';
                                                    } else {
                                                        this.locationError = 'Could not fetch location: ' + err.message;
                                                    }
                                                    this.fetchingLocation = false;
                                                },
                                                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                                            );
                                        }
                                     }">
                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-bold text-lg text-indigo-700">{{ $meter->meter_number }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $meter->status === 'issue' ? 'bg-red-100 text-red-800' : ($meter->status === 'stock' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($meter->status) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-1">
                                            Group: {{ $meter->group ? $meter->group->name : 'N/A' }}
                                        </div>
                                        @if(Auth::user()->role === 'contractor' && $meter->currentWorker)
                                            <div class="text-xs text-indigo-600 font-medium mb-2">
                                                Worker: {{ $meter->currentWorker->name }}
                                            </div>
                                        @elseif(Auth::user()->role === 'contractor' && !$meter->currentWorker)
                                            <div class="text-xs text-amber-600 font-medium mb-2">
                                                ⚠ No worker assigned
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-2 mt-2">
                                        <!-- Install Button - Opens Options -->
                                        <button 
                                            @click="showInstallModal = !showInstallModal"
                                            class="flex-1 bg-green-600 text-white py-2 rounded text-sm font-medium hover:bg-green-700 transition"
                                        >
                                            ✓ Mark Installed
                                        </button>

                                        <!-- Report Issue Button -->
                                        <button 
                                            onclick="if(pct = prompt('Describe the issue:')) { 
                                                document.getElementById('issue-remark-{{ $meter->id }}').value = pct;
                                                document.getElementById('issue-form-{{ $meter->id }}').submit(); 
                                            }"
                                            class="bg-red-100 text-red-700 py-2 px-3 rounded text-sm font-medium hover:bg-red-200 border border-red-200"
                                        >
                                            Issue
                                        </button>

                                        <!-- Request Return Button -->
                                        <button 
                                            onclick="if(reason = prompt('Reason for return:')) { 
                                                document.getElementById('return-reason-{{ $meter->id }}').value = reason;
                                                document.getElementById('return-form-{{ $meter->id }}').submit(); 
                                            }"
                                            class="bg-gray-100 text-gray-700 py-2 px-3 rounded text-sm font-medium hover:bg-gray-200 border border-gray-200"
                                        >
                                            Return
                                        </button>

                                        <!-- Hidden forms for Issue and Return -->
                                        <form id="issue-form-{{ $meter->id }}" method="POST" action="{{ route('worker.meter.update', $meter->id) }}" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="issue">
                                            <input type="hidden" name="remark" id="issue-remark-{{ $meter->id }}">
                                        </form>

                                        <form id="return-form-{{ $meter->id }}" method="POST" action="{{ route('return.store') }}" class="hidden">
                                            @csrf
                                            <input type="hidden" name="meter_id" value="{{ $meter->id }}">
                                            <input type="hidden" name="reason" id="return-reason-{{ $meter->id }}">
                                        </form>
                                    </div>

                                    <!-- Installation Modal -->
                                    <div x-show="showInstallModal" x-transition class="mt-4 border-t pt-4" x-data="{
                                        activePanel: '',
                                        installType: 'nsc',
                                        previews: [],
                                        detailPreviews: [],
                                        compressing: false,
                                        compressInfo: '',
                                        async handleFiles(event, target) {
                                            const input = event.target;
                                            if (!input.files || input.files.length === 0) return;
                                            this.compressing = true;
                                            this.compressInfo = '⚡ Optimizing photos...';
                                            
                                            let originalTotal = 0;
                                            let compressedTotal = 0;
                                            for (const f of input.files) originalTotal += f.size;
                                            
                                            const compressed = await window.compressImageFileList(input.files, { maxWidth: 1600, maxHeight: 1600, quality: 0.82 });
                                            for (const f of compressed) compressedTotal += f.size;
                                            
                                            const dt = new DataTransfer();
                                            compressed.forEach(f => dt.items.add(f));
                                            input.files = dt.files;
                                            
                                            const urls = compressed.map(f => URL.createObjectURL(f));
                                            if (target === 'quick') {
                                                this.previews = urls;
                                            } else {
                                                this.detailPreviews = urls;
                                            }
                                            
                                            const savedPct = Math.round((1 - compressedTotal / (originalTotal || 1)) * 100);
                                            this.compressInfo = `${compressed.length} photo(s) optimized: ${window.formatBytes(originalTotal)} → ${window.formatBytes(compressedTotal)} (${savedPct > 0 ? savedPct + '% saved' : 'optimized'})`;
                                            this.compressing = false;
                                        }
                                    }">
                                        <div class="text-sm font-semibold text-gray-700 mb-3">Installation Options</div>

                                        <!-- Type Selection (shared between both panels) -->
                                        <div class="flex rounded-lg overflow-hidden border border-gray-300 mb-3">
                                            <button type="button" @click="installType = 'nsc'"
                                                :class="installType === 'nsc' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                                                class="flex-1 py-2.5 text-xs font-semibold transition">
                                                🆕 NSC
                                            </button>
                                            <button type="button" @click="installType = 'replacement'"
                                                :class="installType === 'replacement' ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'"
                                                class="flex-1 py-2.5 text-xs font-semibold transition">
                                                🔄 Replacement
                                            </button>
                                        </div>

                                        <!-- Option 1: Quick Install -->
                                        <div class="mb-2">
                                            <button type="button" @click="activePanel = activePanel === 'quick' ? '' : 'quick'"
                                                class="w-full py-2.5 rounded-lg text-sm font-medium transition flex items-center justify-center space-x-2"
                                                :class="activePanel === 'quick' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'">
                                                <span>⚡</span>
                                                <span>Quick Install</span>
                                                <svg :class="activePanel === 'quick' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="activePanel === 'quick'" x-transition class="mt-2 bg-gray-50 p-3 rounded-lg">
                                                <form method="POST" action="{{ route('worker.meter.update', $meter->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="installed">
                                                    <input type="hidden" name="install_mode" value="quick">
                                                    <input type="hidden" name="install_type" :value="installType">

                                                    <!-- Replacement Quick Fields (from config) -->
                                                    <div x-show="installType === 'replacement'" x-transition class="space-y-2 mb-3">
                                                        @if($fieldConfigs->has('ca_number') && $fieldConfigs['ca_number']->is_enabled)
                                                        <input type="text" name="ca_number" placeholder="{{ $fieldConfigs['ca_number']->label }} {{ $fieldConfigs['ca_number']->is_required ? '*' : '' }}"
                                                            :required="installType === 'replacement' && {{ $fieldConfigs['ca_number']->is_required ? 'true' : 'false' }}"
                                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-orange-500 focus:ring-orange-500">
                                                        @endif
                                                        @if($fieldConfigs->has('old_meter_number') && $fieldConfigs['old_meter_number']->is_enabled)
                                                        <input type="text" name="old_meter_number" placeholder="{{ $fieldConfigs['old_meter_number']->label }} {{ $fieldConfigs['old_meter_number']->is_required ? '*' : '' }}"
                                                            :required="installType === 'replacement' && {{ $fieldConfigs['old_meter_number']->is_required ? 'true' : 'false' }}"
                                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-orange-500 focus:ring-orange-500">
                                                        @endif
                                                    </div>

                                                    <!-- Photos -->
                                                    <div class="mb-3">
                                                        <label class="block text-xs font-medium text-gray-600 mb-1">📷 Photos <span class="text-gray-400">(optional)</span></label>
                                                        <input type="file" name="install_images[]" multiple accept="image/*" capture="environment"
                                                            @change="handleFiles($event, 'quick')"
                                                            class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                        <div x-show="compressing" class="text-xs text-indigo-600 font-medium animate-pulse mt-1.5 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                            <span>Optimizing photos...</span>
                                                        </div>
                                                        <div x-show="!compressing && compressInfo && previews.length > 0" class="text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-2 py-1 mt-1.5 font-medium flex items-center gap-1">
                                                            <span>✓</span>
                                                            <span x-text="compressInfo"></span>
                                                        </div>
                                                        <div x-show="previews.length > 0" class="flex flex-wrap gap-2 mt-2">
                                                            <template x-for="(src, i) in previews" :key="i">
                                                                <img :src="src" class="w-14 h-14 object-cover rounded-md border">
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <button type="submit" :disabled="compressing"
                                                        class="w-full text-white py-2.5 rounded-lg text-sm font-medium transition flex items-center justify-center space-x-2"
                                                        :class="compressing ? 'opacity-50 cursor-not-allowed bg-gray-400' : (installType === 'replacement' ? 'bg-orange-500 hover:bg-orange-600' : 'bg-emerald-500 hover:bg-emerald-600')">
                                                        <span>⚡</span>
                                                        <span x-text="compressing ? 'Optimizing...' : (installType === 'replacement' ? 'Quick Replacement' : 'Quick Install')"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Option 2: Install with Details -->
                                        <div class="mb-2">
                                            <button type="button" @click="activePanel = activePanel === 'detailed' ? '' : 'detailed'"
                                                class="w-full py-2.5 rounded-lg text-sm font-medium transition flex items-center justify-center space-x-2"
                                                :class="activePanel === 'detailed' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'">
                                                <span>📝</span>
                                                <span>Install with Details</span>
                                                <svg :class="activePanel === 'detailed' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="activePanel === 'detailed'" x-transition class="mt-2">
                                                <form method="POST" action="{{ route('worker.meter.update', $meter->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="installed">
                                                    <input type="hidden" name="install_mode" value="detailed">
                                                    <input type="hidden" name="install_type" :value="installType">

                                                    <div class="space-y-3 bg-gray-50 p-3 rounded-lg">

                                                        <!-- NSC Fields -->
                                                        <div x-show="installType === 'nsc'" x-transition>
                                                            @if($fieldConfigs->has('request_number') && $fieldConfigs['request_number']->is_enabled)
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                    {{ $fieldConfigs['request_number']->label }}
                                                                    @if($fieldConfigs['request_number']->is_required)
                                                                        <span class="text-red-500">*</span>
                                                                    @else
                                                                        <span class="text-gray-400">(optional)</span>
                                                                    @endif
                                                                </label>
                                                                <input type="text" name="request_number" 
                                                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                                                    placeholder="Enter {{ strtolower($fieldConfigs['request_number']->label) }}"
                                                                    :required="installType === 'nsc' && {{ $fieldConfigs['request_number']->is_required ? 'true' : 'false' }}">
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <!-- Replacement Fields (from config) -->
                                                        <div x-show="installType === 'replacement'" x-transition class="space-y-3">
                                                            @if($fieldConfigs->has('ca_number') && $fieldConfigs['ca_number']->is_enabled)
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                    {{ $fieldConfigs['ca_number']->label }}
                                                                    @if($fieldConfigs['ca_number']->is_required)
                                                                        <span class="text-red-500">*</span>
                                                                    @else
                                                                        <span class="text-gray-400">(optional)</span>
                                                                    @endif
                                                                </label>
                                                                <input type="text" name="ca_number"
                                                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-orange-500 focus:ring-orange-500"
                                                                    placeholder="Enter {{ strtolower($fieldConfigs['ca_number']->label) }}"
                                                                    :required="installType === 'replacement' && {{ $fieldConfigs['ca_number']->is_required ? 'true' : 'false' }}">
                                                            </div>
                                                            @endif
                                                            @if($fieldConfigs->has('old_meter_number') && $fieldConfigs['old_meter_number']->is_enabled)
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                    {{ $fieldConfigs['old_meter_number']->label }}
                                                                    @if($fieldConfigs['old_meter_number']->is_required)
                                                                        <span class="text-red-500">*</span>
                                                                    @else
                                                                        <span class="text-gray-400">(optional)</span>
                                                                    @endif
                                                                </label>
                                                                <input type="text" name="old_meter_number"
                                                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-orange-500 focus:ring-orange-500"
                                                                    placeholder="Enter {{ strtolower($fieldConfigs['old_meter_number']->label) }}"
                                                                    :required="installType === 'replacement' && {{ $fieldConfigs['old_meter_number']->is_required ? 'true' : 'false' }}">
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <!-- Shared Fields: Mobile -->
                                                        @if($fieldConfigs->has('mobile_number') && $fieldConfigs['mobile_number']->is_enabled)
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ $fieldConfigs['mobile_number']->label }}
                                                                @if($fieldConfigs['mobile_number']->is_required)
                                                                    <span class="text-red-500">*</span>
                                                                @else
                                                                    <span class="text-gray-400">(optional)</span>
                                                                @endif
                                                            </label>
                                                            <input type="tel" name="mobile_number" 
                                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                                                placeholder="Enter mobile number"
                                                                {{ $fieldConfigs['mobile_number']->is_required ? 'required' : '' }}>
                                                        </div>
                                                        @endif

                                                        <!-- Shared Fields: Location -->
                                                        @if($fieldConfigs->has('location') && $fieldConfigs['location']->is_enabled)
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ $fieldConfigs['location']->label }}
                                                                @if($fieldConfigs['location']->is_required)
                                                                    <span class="text-red-500">*</span>
                                                                @else
                                                                    <span class="text-gray-400">(optional)</span>
                                                                @endif
                                                            </label>
                                                            <button type="button" @click="fetchLocation()" 
                                                                :disabled="fetchingLocation"
                                                                class="w-full bg-rose-600 text-white py-2 rounded-md text-xs font-semibold hover:bg-rose-700 transition shadow-sm mb-2 flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-wait">
                                                                <template x-if="fetchingLocation">
                                                                    <span class="flex items-center space-x-2">
                                                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                                        </svg>
                                                                        <span>Fetching location...</span>
                                                                    </span>
                                                                </template>
                                                                <template x-if="!fetchingLocation">
                                                                    <span>📍 Fetch Current Location</span>
                                                                </template>
                                                            </button>
                                                            <p x-show="locationError" x-text="locationError" class="text-xs text-red-500 mb-1"></p>
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <input type="text" name="latitude" x-model="latitude"
                                                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                                                    placeholder="Latitude"
                                                                    {{ $fieldConfigs['location']->is_required ? 'required' : '' }}>
                                                                <input type="text" name="longitude" x-model="longitude"
                                                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                                                    placeholder="Longitude"
                                                                    {{ $fieldConfigs['location']->is_required ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <!-- Shared Fields: Remark -->
                                                        @if($fieldConfigs->has('remark') && $fieldConfigs['remark']->is_enabled)
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                {{ $fieldConfigs['remark']->label }}
                                                                @if($fieldConfigs['remark']->is_required)
                                                                    <span class="text-red-500">*</span>
                                                                @else
                                                                    <span class="text-gray-400">(optional)</span>
                                                                @endif
                                                            </label>
                                                            <textarea name="install_remark" rows="2"
                                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                                                placeholder="Any additional remarks"
                                                                {{ $fieldConfigs['remark']->is_required ? 'required' : '' }}></textarea>
                                                        </div>
                                                        @endif

                                                        <!-- Images -->
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">📷 Installation Photos <span class="text-gray-400">(optional, max 10)</span></label>
                                                            <input type="file" name="install_images[]" multiple accept="image/*" capture="environment"
                                                                @change="handleFiles($event, 'detailed')"
                                                                class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                            <div x-show="compressing" class="text-xs text-indigo-600 font-medium animate-pulse mt-1.5 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                                <span>Optimizing photos...</span>
                                                            </div>
                                                            <div x-show="!compressing && compressInfo && detailPreviews.length > 0" class="text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-2 py-1 mt-1.5 font-medium flex items-center gap-1">
                                                                <span>✓</span>
                                                                <span x-text="compressInfo"></span>
                                                            </div>
                                                            <div x-show="detailPreviews.length > 0" class="flex flex-wrap gap-2 mt-2">
                                                                <template x-for="(src, i) in detailPreviews" :key="i">
                                                                    <img :src="src" class="w-14 h-14 object-cover rounded-md border">
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <button type="submit" :disabled="compressing"
                                                            class="w-full text-white py-2.5 rounded-lg text-sm font-bold transition"
                                                            :class="compressing ? 'opacity-50 cursor-not-allowed bg-gray-400' : (installType === 'replacement' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700')">
                                                            <span x-text="compressing ? 'Optimizing Photos...' : (installType === 'replacement' ? '🔄 Submit Replacement' : '✓ Submit NSC Install')"></span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Cancel -->
                                        <button @click="showInstallModal = false; activePanel = ''" class="w-full text-gray-500 text-xs hover:text-gray-700 py-1">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    @if($assignedMeters->hasPages())
                    <div class="mt-4">
                        {{ $assignedMeters->withQueryString()->links() }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
