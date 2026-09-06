<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('stock.index') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Meter: {{ $meter->meter_number }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Success/Error -->
            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                </div>
            @endif

            <!-- ═══════════════════════════════════════════ -->
            <!-- 1. METER INFO CARD -->
            <!-- ═══════════════════════════════════════════ -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $meter->meter_number }}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                @php
                                    $statusColors = [
                                        'stock' => 'bg-green-100 text-green-700 border-green-200',
                                        'assigned' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'installed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'issue' => 'bg-red-100 text-red-700 border-red-200',
                                        'returned' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'mi_complete' => 'bg-teal-100 text-teal-700 border-teal-200',
                                        'mi_issue' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'return_pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-sm rounded-full font-semibold border {{ $statusColors[$meter->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($meter->status) }}
                                </span>
                                @if($meter->group)
                                    <a href="{{ route('stock.groups.show', $meter->group) }}" class="text-sm text-indigo-600 hover:underline">
                                        📦 {{ $meter->group->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">Ungrouped</span>
                                @endif
                                @if($meter->lot)
                                    <a href="{{ route('lots.show', $meter->lot) }}" class="text-sm px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium hover:bg-indigo-100">
                                        {{ $meter->lot->lot_number }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">No Lot</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 text-right">
                            @if($meter->currentWorker)
                                <div>Worker: <a href="{{ route('workers.show', $meter->currentWorker) }}" class="font-semibold text-indigo-600 hover:underline">{{ $meter->currentWorker->name }}</a></div>
                            @endif
                            <div class="text-xs text-gray-400 mt-1">Imported: {{ $meter->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════ -->
            <!-- 2. ADMIN ACTIONS: Status, Reassign, Lot, Edit, Delete -->
            <!-- ═══════════════════════════════════════════ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mx-4 sm:mx-0">

                <!-- Change Status -->
                <div class="bg-white rounded-lg shadow-sm p-4" x-data="{ selectedStatus: '{{ $meter->status }}' }">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Change Status</h3>
                    <form method="POST" action="{{ route('stock.changeStatus', $meter) }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-2 mb-3">
                            <select name="status" x-model="selectedStatus"
                                class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(['stock', 'assigned', 'installed', 'issue', 'returned'] as $s)
                                    <option value="{{ $s }}" {{ $meter->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">Update</button>
                        </div>

                        <!-- Installation details (shown when selecting installed) -->
                        <div x-show="selectedStatus === 'installed'" x-cloak class="space-y-2 pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-500">Optional: Add installation details</p>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="request_number" placeholder="Request Number" value="{{ $meter->installationDetail?->request_number }}"
                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="text" name="mobile_number" placeholder="Mobile Number" value="{{ $meter->installationDetail?->mobile_number }}"
                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="text" name="latitude" placeholder="Latitude" value="{{ $meter->installationDetail?->latitude }}"
                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="text" name="longitude" placeholder="Longitude" value="{{ $meter->installationDetail?->longitude }}"
                                    class="rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <textarea name="install_remark" placeholder="Remark" rows="2"
                                class="w-full rounded-md border-gray-300 shadow-sm text-xs p-2 border focus:border-indigo-500 focus:ring-indigo-500">{{ $meter->installationDetail?->remark }}</textarea>
                        </div>
                    </form>
                </div>

                <!-- Reassign Worker -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        Assign / Reassign Worker
                    </h3>
                    <form method="POST" action="{{ route('stock.reassign', $meter) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="worker_id" class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select worker...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ $meter->current_worker_id == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">Assign</button>
                    </form>
                </div>

                <!-- Change Lot -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Change Lot</h3>
                    <form method="POST" action="{{ route('stock.changeLot', $meter) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="lot_id" class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">No Lot</option>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}" {{ $meter->lot_id == $lot->id ? 'selected' : '' }}>{{ $lot->lot_number }} {{ $lot->description ? '— ' . $lot->description : '' }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">Move</button>
                    </form>
                </div>

                <!-- Edit Meter Number -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Edit Meter Number</h3>
                    <form method="POST" action="{{ route('stock.update', $meter) }}" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="meter_number" value="{{ $meter->meter_number }}"
                            class="flex-1 rounded-md border-gray-300 shadow-sm text-sm p-2 border focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">Save</button>
                    </form>
                </div>

                <!-- Delete Meter -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Delete Meter</h3>
                    @if($meter->status === 'stock')
                        <form method="POST" action="{{ route('stock.destroy', $meter) }}"
                               onsubmit="return confirm('Are you sure you want to delete meter {{ $meter->meter_number }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition">
                                Delete from Stock
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('stock.destroy', $meter) }}"
                               onsubmit="return confirm('⚠️ WARNING: This meter is currently {{ strtoupper($meter->status) }}. Deleting it will permanently erase all associated assignments, installation records, image uploads, and logs. This action CANNOT be undone. Are you sure you want to force delete this meter and all its work history?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-sm font-semibold rounded-md transition">
                                ⚠️ Force Delete Meter & History
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-2 text-center">
                            Allows cleaning up mistakes. Removes all logs, images, return requests, and assignments.
                        </p>
                    @endif
                </div>
            </div>

            <!-- ═══════════════════════════════════════════ -->
            <!-- 3. INSTALLATION DETAILS -->
            <!-- ═══════════════════════════════════════════ -->
            @if($meter->installationDetail)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">
                            📋 Installation Details
                            @if($meter->installationDetail->install_type === 'replacement')
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-700 font-semibold normal-case">🔄 Replacement</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 font-semibold normal-case">🆕 NSC</span>
                            @endif
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($meter->installationDetail->install_type === 'replacement')
                            <div>
                                <div class="text-xs text-gray-400 mb-1">CA Number</div>
                                <div class="text-sm text-gray-900"><x-copyable :value="$meter->installationDetail->ca_number" /></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Old Meter Number</div>
                                <div class="text-sm text-gray-900"><x-copyable :value="$meter->installationDetail->old_meter_number" /></div>
                            </div>
                            @else
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Request Number</div>
                                <div class="text-sm text-gray-900"><x-copyable :value="$meter->installationDetail->request_number" /></div>
                            </div>
                            @endif
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Mobile Number</div>
                                <div class="text-sm text-gray-900"><x-copyable :value="$meter->installationDetail->mobile_number" /></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Installed By</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $meter->installationDetail->worker ? $meter->installationDetail->worker->name : '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Coordinates</div>
                                @if($meter->installationDetail->latitude && $meter->installationDetail->longitude)
                                    @php $coords = $meter->installationDetail->latitude . ', ' . $meter->installationDetail->longitude; @endphp
                                    <x-copyable :value="$coords" :label="'📍 ' . $coords" />
                                    <a href="https://maps.google.com/?q={{ $coords }}" target="_blank"
                                       class="text-xs text-indigo-500 hover:underline ml-1">Open Map ↗</a>
                                @else
                                    <div class="text-sm text-gray-500">—</div>
                                @endif
                            </div>
                            @if($meter->installationDetail->remark)
                                <div class="sm:col-span-2">
                                    <div class="text-xs text-gray-400 mb-1">Remark</div>
                                    <div class="text-sm text-gray-700 bg-gray-50 rounded p-2">{{ $meter->installationDetail->remark }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Installation Photos --}}
                @if($meter->installationDetail->images && $meter->installationDetail->images->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0 mt-4">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">📷 Installation Photos ({{ $meter->installationDetail->images->count() }})</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($meter->installationDetail->images as $img)
                                <div class="relative group">
                                    <a href="{{ Storage::url($img->image_path) }}" target="_blank">
                                        <img src="{{ Storage::url($img->image_path) }}" alt="Installation photo"
                                             class="w-full h-32 object-cover rounded-lg border hover:opacity-90 transition">
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
            @endif

            <!-- ═══════════════════════════════════════════ -->
            <!-- 4. MI (METER ACTIVATION) STATUS -->
            <!-- ═══════════════════════════════════════════ -->
            @if($meter->activation)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">📊 MI Status</h3>
                            @php
                                $miColors = [
                                    'pending'       => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'complete'      => 'bg-green-100 text-green-800 border-green-300',
                                    'issue'         => 'bg-red-100 text-red-800 border-red-300',
                                    'job_not_found' => 'bg-purple-100 text-purple-800 border-purple-300',
                                ];
                                $miLabels = [
                                    'pending'       => 'Pending',
                                    'complete'      => 'Complete',
                                    'issue'         => 'Issue',
                                    'job_not_found' => 'Job Not Found',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-sm rounded-full font-semibold border {{ $miColors[$meter->activation->mi_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $miLabels[$meter->activation->mi_status] ?? ucfirst($meter->activation->mi_status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Worker</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $meter->activation->worker->name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-1">Completed At</div>
                                <div class="text-sm text-gray-900">{{ $meter->activation->completed_at ? $meter->activation->completed_at->format('d M Y, h:i A') : 'Not yet' }}</div>
                            </div>
                            @if($meter->activation->remark)
                                <div class="sm:col-span-2">
                                    <div class="text-xs text-gray-400 mb-1">Remark</div>
                                    <div class="text-sm text-gray-700 bg-gray-50 rounded p-2">{{ $meter->activation->remark }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- MI Images -->
                        @if($meter->activation->images && $meter->activation->images->count() > 0)
                            <div class="mt-4">
                                <div class="text-xs text-gray-400 mb-2">Uploaded Images</div>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    @foreach($meter->activation->images as $image)
                                        <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="block">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="MI Image"
                                                 class="w-full h-20 object-cover rounded border border-gray-200 hover:border-indigo-400 transition">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Link to full MI page -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <a href="{{ route('mi.show', $meter->activation) }}" class="text-sm text-indigo-600 hover:underline font-medium">
                                View full MI details →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ═══════════════════════════════════════════ -->
            <!-- 5. RETURN REQUESTS -->
            <!-- ═══════════════════════════════════════════ -->
            @if($meter->returnRequests && $meter->returnRequests->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">🔄 Return Requests</h3>
                        <div class="space-y-3">
                            @foreach($meter->returnRequests->sortByDesc('created_at') as $return)
                                <div class="border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900">{{ $return->worker->name }}</span>
                                        @php
                                            $retColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $retColors[$return->status] ?? '' }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $return->reason }}</div>
                                    @if($return->admin_remark)
                                        <div class="text-xs text-gray-400 mt-1">Admin: {{ $return->admin_remark }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">{{ $return->created_at->diffForHumans() }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- ═══════════════════════════════════════════ -->
            <!-- 6. ASSIGNMENT HISTORY -->
            <!-- ═══════════════════════════════════════════ -->
            @if($meter->assignments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">👤 Assignment History</h3>
                        <div class="space-y-2">
                            @foreach($meter->assignments->sortByDesc('created_at') as $assignment)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 text-sm">
                                    <div>
                                        <span class="text-gray-600">Assigned to</span>
                                        <span class="font-semibold text-gray-900">{{ $assignment->worker->name }}</span>
                                        <span class="text-gray-400">by {{ $assignment->assigner->name }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $assignment->created_at->diffForHumans() }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- ═══════════════════════════════════════════ -->
            <!-- 7. METER LIFECYCLE & TIMELINE -->
            <!-- ═══════════════════════════════════════════ -->

            {{-- Lifecycle Progress Bar --}}
            @php
                $stages = ['stock' => '📦', 'assigned' => '📋', 'installed' => '⚡', 'mi_complete' => '✅'];
                $stageLabels = ['stock' => 'Stock', 'assigned' => 'Assigned', 'installed' => 'Installed', 'mi_complete' => 'MI Done'];
                $stageOrder = array_keys($stages);
                $currentIdx = array_search($meter->status, $stageOrder);
                if ($currentIdx === false) {
                    $currentIdx = match($meter->status) {
                        'issue', 'mi_issue' => 2,
                        'return_pending', 'returned' => 1,
                        default => 0,
                    };
                }
            @endphp
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-5">🔄 Meter Lifecycle</h3>
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 right-0 top-1/2 h-1 bg-gray-200 rounded -translate-y-1/2 mx-8"></div>
                        <div class="absolute left-0 top-1/2 h-1 bg-gradient-to-r from-indigo-500 to-emerald-500 rounded -translate-y-1/2 mx-8 transition-all"
                             style="width: {{ $currentIdx >= 3 ? '90%' : ($currentIdx * 30) . '%' }}"></div>
                        @foreach($stages as $key => $icon)
                            @php
                                $idx = array_search($key, $stageOrder);
                                $done = $idx <= $currentIdx;
                                $isCurrent = $idx === $currentIdx;
                            @endphp
                            <div class="relative z-10 flex flex-col items-center {{ $done ? '' : 'opacity-40' }}">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shadow-sm border-2 transition-all
                                    {{ $isCurrent ? 'bg-indigo-600 border-indigo-600 text-white ring-4 ring-indigo-100 scale-110' : ($done ? 'bg-white border-emerald-500 text-emerald-600' : 'bg-gray-100 border-gray-300 text-gray-400') }}">
                                    {{ $icon }}
                                </div>
                                <span class="text-xs mt-1.5 font-medium {{ $isCurrent ? 'text-indigo-700' : ($done ? 'text-emerald-700' : 'text-gray-400') }}">
                                    {{ $stageLabels[$key] }}
                                </span>
                                @if($isCurrent)
                                    <span class="absolute -bottom-5 text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Current</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Full Activity Timeline --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">📜 Activity Timeline ({{ $logs->count() }})</h3>
                    @if($logs->count() > 0)
                        <div class="relative">
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gradient-to-b from-indigo-200 via-gray-200 to-gray-100"></div>

                            <div class="space-y-0">
                                @foreach($logs as $index => $log)
                                    @php
                                        $actionConfig = [
                                            'imported'           => ['icon' => '📥', 'color' => 'bg-green-500',   'border' => 'border-l-green-400',   'label' => 'Imported'],
                                            'assigned'           => ['icon' => '📋', 'color' => 'bg-blue-500',    'border' => 'border-l-blue-400',    'label' => 'Assigned'],
                                            'reassigned'         => ['icon' => '🔄', 'color' => 'bg-purple-500',  'border' => 'border-l-purple-400',  'label' => 'Reassigned'],
                                            'status_change'      => ['icon' => '🔀', 'color' => 'bg-yellow-500',  'border' => 'border-l-yellow-400',  'label' => 'Status Changed'],
                                            'mi_status_change'   => ['icon' => '📊', 'color' => 'bg-teal-500',    'border' => 'border-l-teal-400',    'label' => 'MI Updated'],
                                            'installed'          => ['icon' => '⚡', 'color' => 'bg-emerald-500', 'border' => 'border-l-emerald-400', 'label' => 'Installed'],
                                            'return_request'     => ['icon' => '↩️', 'color' => 'bg-orange-500',  'border' => 'border-l-orange-400',  'label' => 'Return Requested'],
                                            'return_requested'   => ['icon' => '↩️', 'color' => 'bg-orange-500',  'border' => 'border-l-orange-400',  'label' => 'Return Requested'],
                                            'return_approved'    => ['icon' => '✅', 'color' => 'bg-green-600',   'border' => 'border-l-green-500',   'label' => 'Return Approved'],
                                            'return_rejected'    => ['icon' => '❌', 'color' => 'bg-red-600',     'border' => 'border-l-red-500',     'label' => 'Return Rejected'],
                                            'returned'           => ['icon' => '📦', 'color' => 'bg-gray-500',    'border' => 'border-l-gray-400',    'label' => 'Returned to Stock'],
                                            'meter_number_changed' => ['icon' => '✏️', 'color' => 'bg-purple-500','border' => 'border-l-purple-400',  'label' => 'Number Changed'],
                                            'lot_change'         => ['icon' => '🏷️', 'color' => 'bg-indigo-500', 'border' => 'border-l-indigo-400',  'label' => 'Lot Changed'],
                                        ];
                                        $cfg = $actionConfig[$log->action] ?? ['icon' => '📝', 'color' => 'bg-gray-400', 'border' => 'border-l-gray-300', 'label' => ucfirst(str_replace('_', ' ', $log->action))];
                                        $ts = \Carbon\Carbon::parse($log->timestamp);
                                    @endphp
                                    <div class="relative pl-10 pb-4">
                                        <div class="absolute left-2.5 top-3 w-4 h-4 rounded-full {{ $cfg['color'] }} ring-3 ring-white shadow-sm flex items-center justify-center">
                                            @if($index === 0)
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            @endif
                                        </div>
                                        <div class="bg-gray-50 rounded-lg border-l-4 {{ $cfg['border'] }} p-3 hover:bg-gray-100 transition {{ $index === 0 ? 'ring-1 ring-indigo-100' : '' }}">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-base">{{ $cfg['icon'] }}</span>
                                                    <span class="text-sm font-semibold text-gray-800">{{ $cfg['label'] }}</span>
                                                    @if($log->user)
                                                        <span class="text-xs text-gray-400">by <span class="text-gray-600 font-medium">{{ $log->user->name }}</span></span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                                    <span title="{{ $ts->format('d M Y, h:i A') }}">{{ $ts->diffForHumans() }}</span>
                                                    <span class="hidden sm:inline text-gray-300">•</span>
                                                    <span class="hidden sm:inline">{{ $ts->format('d M Y, h:i A') }}</span>
                                                </div>
                                            </div>
                                            @if($log->remark)
                                                <p class="text-xs text-gray-600 mt-1.5 pl-7">💬 {{ $log->remark }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📭</div>
                            <p class="text-sm text-gray-500">No activity logs recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
