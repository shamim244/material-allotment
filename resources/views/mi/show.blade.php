<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('mi.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                MI - {{ $activation->meter->meter_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Meter Info Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900"><a href="{{ route('stock.show', $activation->meter) }}" class="text-indigo-600 hover:underline">{{ $activation->meter->meter_number }}</a></h3>
                            <p class="text-sm text-gray-500">Worker: <a href="{{ route('workers.show', $activation->worker) }}" class="text-indigo-600 hover:underline">{{ $activation->worker->name }}</a></p>
                        </div>
                        @php
                            $statusColors = [
                                'pending'       => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'issue'         => 'bg-red-100 text-red-800 border-red-300',
                                'complete'      => 'bg-green-100 text-green-800 border-green-300',
                                'job_not_found' => 'bg-purple-100 text-purple-800 border-purple-300',
                            ];
                            $statusLabels = [
                                'pending'       => 'Pending',
                                'issue'         => 'Issue',
                                'complete'      => 'Complete',
                                'job_not_found' => 'Job Not Found',
                            ];
                        @endphp
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full border {{ $statusColors[$activation->mi_status] ?? '' }}">
                            {{ $statusLabels[$activation->mi_status] ?? ucfirst($activation->mi_status) }}
                        </span>
                    </div>

                    @if($activation->remark)
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <p class="text-xs text-gray-500 mb-1">Current Remark</p>
                        <p class="text-sm text-gray-700">{{ $activation->remark }}</p>
                    </div>
                    @endif

                    @if($activation->completed_at)
                    <div class="text-xs text-green-600">
                        ✓ Completed: {{ $activation->completed_at->format('d M Y, h:i A') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Installation Details (View & Edit) -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6" x-data="{ editing: {{ $installationDetail ? 'false' : 'true' }}, fetching: false }">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-gray-700">Installation Details</h4>
                        @if($installationDetail)
                        <button @click="editing = !editing" class="text-xs text-indigo-600 hover:underline" x-text="editing ? 'Cancel' : 'Edit'"></button>
                        @endif
                    </div>

                    {{-- Display existing details --}}
                    <div x-show="!editing">
                        @if($installationDetail)
                        <div class="mb-3">
                            @if($installationDetail->install_type === 'replacement')
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 font-semibold">🔄 Replacement</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 font-semibold">🆕 NSC</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            @if($installationDetail->install_type === 'replacement')
                            <div>
                                <span class="text-xs text-gray-500 block">CA Number</span>
                                <x-copyable :value="$installationDetail->ca_number" />
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Old Meter Number</span>
                                <x-copyable :value="$installationDetail->old_meter_number" />
                            </div>
                            @else
                            <div>
                                <span class="text-xs text-gray-500 block">Request Number</span>
                                <x-copyable :value="$installationDetail->request_number" />
                            </div>
                            @endif
                            <div>
                                <span class="text-xs text-gray-500 block">Mobile Number</span>
                                <x-copyable :value="$installationDetail->mobile_number" />
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500 block">Coordinates</span>
                                @if($installationDetail->latitude && $installationDetail->longitude)
                                    @php $coords = $installationDetail->latitude . ', ' . $installationDetail->longitude; @endphp
                                    <x-copyable :value="$coords" :label="'📍 ' . $coords" />
                                    <a href="https://maps.google.com/?q={{ $coords }}" target="_blank"
                                       class="text-xs text-indigo-500 hover:underline ml-1">Open Map ↗</a>
                                @else
                                    <span class="font-medium text-gray-400">—</span>
                                @endif
                            </div>
                            @if($installationDetail->remark)
                            <div class="col-span-2">
                                <span class="text-xs text-gray-500 block">Remark</span>
                                <x-copyable :value="$installationDetail->remark" />
                            </div>
                            @endif
                        </div>
                        @else
                        <p class="text-sm text-gray-400">No installation details added yet.</p>
                        @endif
                    </div>

                    {{-- Edit/Add form --}}
                    <div x-show="editing" x-transition>
                        <form method="POST" action="{{ route('mi.details.update', $activation->id) }}">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Request Number</label>
                                        <input type="text" name="request_number" value="{{ $installationDetail->request_number ?? '' }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter request number">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Mobile Number</label>
                                        <input type="text" name="mobile_number" value="{{ $installationDetail->mobile_number ?? '' }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Enter mobile number">
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-xs font-medium text-gray-600">Location</label>
                                        <button type="button" @click="
                                            if (!window.isSecureContext) { alert('Location requires HTTPS. Please access this site via https:// or contact admin.'); return; }
                                            fetching = true;
                                            if (navigator.geolocation) {
                                                navigator.geolocation.getCurrentPosition(
                                                    pos => { $refs.lat.value = pos.coords.latitude.toFixed(6); $refs.lng.value = pos.coords.longitude.toFixed(6); fetching = false; },
                                                    err => { 
                                                        if (err.code === 1) {
                                                            alert('Location permission denied. Please allow location access in your browser settings.');
                                                        } else if (err.code === 2) {
                                                            alert('Location unavailable. Please check your GPS/network.');
                                                        } else if (err.code === 3) {
                                                            alert('Location request timed out. Please try again.');
                                                        } else {
                                                            alert('Location error: ' + err.message); 
                                                        }
                                                        fetching = false; 
                                                    },
                                                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                                                );
                                            } else { alert('Geolocation not supported by your browser.'); fetching = false; }
                                        " :disabled="fetching"
                                            class="inline-flex items-center px-3 py-1.5 bg-rose-600 text-white text-xs font-semibold rounded-md hover:bg-rose-700 transition shadow-sm disabled:opacity-50 disabled:cursor-wait">
                                            <template x-if="fetching">
                                                <span class="flex items-center space-x-1.5">
                                                    <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                    </svg>
                                                    <span>Fetching...</span>
                                                </span>
                                            </template>
                                            <template x-if="!fetching">
                                                <span>📍 Fetch GPS</span>
                                            </template>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" name="latitude" x-ref="lat" value="{{ $installationDetail->latitude ?? '' }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Latitude">
                                        <input type="text" name="longitude" x-ref="lng" value="{{ $installationDetail->longitude ?? '' }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Longitude">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Remark <span class="text-gray-400">(optional)</span></label>
                                    <textarea name="detail_remark" rows="2"
                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Add a remark...">{{ $installationDetail->remark ?? '' }}</textarea>
                                </div>

                                <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                                    {{ $installationDetail ? 'Update Details' : 'Save Details' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Update Status Form -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Update MI Status</h4>

                    {{-- Show current issue type if any --}}
                    @if($activation->mi_status === 'issue' && ($activation->issueType || $activation->issue_detail))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                            <div class="text-xs text-red-500 mb-1">Current Issue</div>
                            <div class="text-sm font-semibold text-red-700">
                                {{ $activation->issueType ? $activation->issueType->name : 'Other' }}
                                @if($activation->issue_detail)
                                    <span class="font-normal text-red-600">— {{ $activation->issue_detail }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mi.update', $activation->id) }}"
                          x-data="{
                              status: '{{ $activation->mi_status }}',
                              issueTypeId: '{{ $activation->issue_type_id ?? '' }}',
                              isOther: {{ ($activation->mi_status === 'issue' && !$activation->issue_type_id && $activation->issue_detail) ? 'true' : 'false' }}
                          }">
                        @csrf
                        @method('PATCH')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select name="mi_status" x-model="status"
                                    @change="if(status !== 'issue') { issueTypeId = ''; isOther = false; }"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending">Pending</option>
                                    <option value="issue">Any Issue</option>
                                    <option value="job_not_found">Job Not Found</option>
                                    <option value="complete">Complete</option>
                                </select>
                            </div>

                            {{-- Issue Type Selection — shown only when status = issue --}}
                            <div x-show="status === 'issue'" x-transition x-cloak class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Issue Type <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($issueTypes as $type)
                                            <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer transition text-sm"
                                                   :class="issueTypeId == '{{ $type->id }}' ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 hover:border-gray-300'">
                                                <input type="radio" name="issue_type_id" value="{{ $type->id }}"
                                                       x-model="issueTypeId" @click="isOther = false"
                                                       class="text-red-600 focus:ring-red-500">
                                                {{ $type->name }}
                                            </label>
                                        @endforeach
                                        <label class="flex items-center gap-2 p-2.5 border rounded-lg cursor-pointer transition text-sm"
                                               :class="isOther ? 'border-orange-400 bg-orange-50 text-orange-700' : 'border-gray-200 hover:border-gray-300'">
                                            <input type="radio" name="issue_type_id" value=""
                                                   @click="isOther = true; issueTypeId = ''"
                                                   :checked="isOther"
                                                   class="text-orange-600 focus:ring-orange-500">
                                            Other
                                        </label>
                                    </div>
                                </div>

                                {{-- Other — manual input --}}
                                <div x-show="isOther" x-transition>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Describe the issue <span class="text-red-500">*</span></label>
                                    <input type="text" name="issue_detail" 
                                        value="{{ $activation->issue_detail }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="e.g. Consumer refused installation...">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remark <span class="text-gray-400">(optional)</span></label>
                                <textarea name="remark" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add a remark...">{{ $activation->remark }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Upload Images</h4>
                    <form method="POST" action="{{ route('mi.upload', $activation->id) }}" enctype="multipart/form-data"
                          x-data="{
                              previews: [],
                              fileCount: 0,
                              compressing: false,
                              compressInfo: '',
                              async handleFiles(event) {
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

                                  this.previews = compressed.map(f => URL.createObjectURL(f));
                                  this.fileCount = compressed.length;

                                  const savedPct = Math.round((1 - compressedTotal / (originalTotal || 1)) * 100);
                                  this.compressInfo = `${compressed.length} photo(s) optimized: ${window.formatBytes(originalTotal)} → ${window.formatBytes(compressedTotal)} (${savedPct > 0 ? savedPct + '% saved' : 'optimized'})`;
                                  this.compressing = false;
                              }
                          }">
                        @csrf
                        
                        <div class="space-y-4">
                            <!-- File/Camera Upload -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <div class="flex items-center justify-center py-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-400 transition">
                                        <div class="text-center">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-xs text-gray-500 mt-1">📁 Choose Files</p>
                                        </div>
                                    </div>
                                    <input type="file" name="images[]" multiple accept="image/*" class="hidden"
                                        @change="handleFiles($event)">
                                </label>

                                <label class="flex-1 cursor-pointer">
                                    <div class="flex items-center justify-center py-4 border-2 border-dashed border-blue-300 rounded-lg hover:border-blue-400 transition bg-blue-50">
                                        <div class="text-center">
                                            <svg class="mx-auto h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <p class="text-xs text-blue-500 mt-1">📷 Camera</p>
                                        </div>
                                    </div>
                                    <input type="file" name="images[]" accept="image/*" capture="environment" class="hidden"
                                        @change="handleFiles($event)">
                                </label>
                            </div>

                            <!-- Compression status indicator -->
                            <div x-show="compressing" class="text-xs text-blue-600 font-medium animate-pulse flex items-center gap-1.5 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Optimizing photos for fast upload...</span>
                            </div>

                            <!-- Compression savings badge -->
                            <div x-show="!compressing && compressInfo && previews.length > 0" class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 font-medium flex items-center gap-1.5">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span x-text="compressInfo"></span>
                            </div>

                            <!-- Preview -->
                            <div x-show="previews.length > 0" class="grid grid-cols-3 gap-2">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <img :src="preview" class="w-full h-24 object-cover rounded-lg border">
                                </template>
                            </div>

                            <p x-show="fileCount > 0" class="text-xs text-gray-500" x-text="fileCount + ' file(s) selected'"></p>

                            <button type="submit" x-show="fileCount > 0" :disabled="compressing"
                                class="w-full text-white py-2.5 rounded-lg text-sm font-medium transition"
                                :class="compressing ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'">
                                <span x-text="compressing ? 'Optimizing Photos...' : 'Upload Images'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Installation Photos -->
            @if($installationDetail && $installationDetail->images && $installationDetail->images->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">📷 Installation Photos ({{ $installationDetail->images->count() }})</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($installationDetail->images as $img)
                        <div class="relative group">
                            <a href="{{ Storage::url($img->image_path) }}" target="_blank">
                                <img src="{{ Storage::url($img->image_path) }}" alt="Installation photo"
                                     class="w-full h-32 object-cover rounded-lg border hover:opacity-90 transition cursor-pointer">
                            </a>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-2 rounded-b-lg">
                                <div class="text-xs text-white">{{ $img->uploader->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-white/70">{{ $img->created_at->format('d M, H:i') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Existing MI Images Gallery -->
            @if($activation->images->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">🔍 MI Images ({{ $activation->images->count() }})</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($activation->images as $img)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $img->image_path) }}" 
                                 alt="MI Image" 
                                 class="w-full h-32 object-cover rounded-lg border cursor-pointer"
                                 onclick="window.open(this.src, '_blank')">
                            
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg">
                                {{ $img->uploader->name }} · {{ $img->created_at->diffForHumans() }}
                            </div>

                            @if(Auth::user()->role === 'contractor' || $img->uploaded_by === Auth::id())
                            <form method="POST" action="{{ route('mi.image.delete', $img->id) }}" 
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition"
                                  onsubmit="return confirm('Delete this image?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-700">
                                    ✕
                                </button>
                            </form>
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
