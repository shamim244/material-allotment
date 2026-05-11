<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Meters') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="assignmentForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="mx-4 sm:mx-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mx-4 sm:mx-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                    @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('assignments.store') }}">
                @csrf

                <!-- ═══════════════════════════════════════ -->
                <!-- 1. SELECT WORKER -->
                <!-- ═══════════════════════════════════════ -->
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0 mb-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">1. Select Worker</h3>
                    <select name="worker_id" x-model="workerId" required
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Choose Worker --</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}">
                                {{ $worker->name }} — {{ $worker->active_meter_count }} meters assigned
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- 2. ASSIGNMENT TYPE -->
                <!-- ═══════════════════════════════════════ -->
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0 mb-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">2. Assignment Type</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="setType('group')"
                            :class="assignmentType === 'group' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            class="px-4 py-3 rounded-lg border text-sm font-medium transition text-center">
                            📦 By Group
                        </button>
                        <button type="button" @click="setType('individual')"
                            :class="assignmentType === 'individual' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            class="px-4 py-3 rounded-lg border text-sm font-medium transition text-center">
                            🔢 Individual
                        </button>
                        <button type="button" @click="setType('loose')"
                            :class="assignmentType === 'loose' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            class="px-4 py-3 rounded-lg border text-sm font-medium transition text-center">
                            📋 Loose
                        </button>
                    </div>
                    <input type="hidden" name="assignment_type" :value="assignmentType">
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- 3A. GROUP SELECTION + METER PREVIEW -->
                <!-- ═══════════════════════════════════════ -->
                <div x-show="assignmentType === 'group'" x-cloak class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0 mb-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">3. Select Group</h3>

                    <select x-model="selectedGroupId" @change="loadGroupMeters()" name="group_id"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500 mb-4">
                        <option value="">-- Choose Group --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">
                                {{ $group->name }} ({{ ucfirst($group->type) }}) — {{ $group->meters_count }} in stock
                            </option>
                        @endforeach
                    </select>

                    <!-- Loading -->
                    <div x-show="loadingGroup" class="text-center py-4">
                        <div class="text-sm text-gray-400">Loading meters...</div>
                    </div>

                    <!-- Meter Preview -->
                    <div x-show="groupMeters.length > 0 && !loadingGroup">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-gray-700">
                                <span x-text="groupName"></span>
                                <span class="text-gray-400 ml-1" x-text="'(' + groupMeters.length + ' meters)'"></span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="selectAllGroup()" class="text-xs text-indigo-600 hover:underline">Select All</button>
                                <button type="button" @click="deselectAllGroup()" class="text-xs text-gray-500 hover:underline">Deselect All</button>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-60 overflow-y-auto">
                            <template x-for="meter in groupMeters" :key="meter.id">
                                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer transition">
                                    <input type="checkbox" name="meter_ids[]" :value="meter.id" x-model="selectedMeterIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-mono text-gray-800" x-text="meter.meter_number"></span>
                                </label>
                            </template>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <span x-text="selectedMeterIds.length"></span> of <span x-text="groupMeters.length"></span> selected
                        </div>
                    </div>
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- 3B. INDIVIDUAL METERS (searchable checkboxes) -->
                <!-- ═══════════════════════════════════════ -->
                <div x-show="assignmentType === 'individual'" x-cloak class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0 mb-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">3. Select Meters</h3>

                    <!-- Search -->
                    <input type="text" x-model="individualSearch" placeholder="Search meter number..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5 border focus:border-indigo-500 focus:ring-indigo-500 font-mono mb-3">

                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm text-gray-600">
                            <span x-text="filteredIndividualMeters().length"></span> meters found
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="selectAllIndividual()" class="text-xs text-indigo-600 hover:underline">Select All Visible</button>
                            <button type="button" @click="deselectAllIndividual()" class="text-xs text-gray-500 hover:underline">Deselect All</button>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-72 overflow-y-auto">
                        <template x-for="meter in filteredIndividualMeters()" :key="meter.id">
                            <label class="flex items-center justify-between px-3 py-2 hover:bg-gray-50 cursor-pointer transition">
                                <div class="flex items-center">
                                    <input type="checkbox" name="individual_meter_ids[]" :value="meter.id" x-model="selectedIndividualIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-mono text-gray-800" x-text="meter.meter_number"></span>
                                </div>
                                <span x-show="meter.group_name" class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full" x-text="meter.group_name"></span>
                            </label>
                        </template>
                        <div x-show="filteredIndividualMeters().length === 0" class="px-3 py-4 text-center text-sm text-gray-400">
                            No meters match your search.
                        </div>
                    </div>

                    <div class="mt-2 text-xs text-gray-500">
                        <span x-text="selectedIndividualIds.length"></span> selected
                    </div>
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- 3C. LOOSE (UNGROUPED) METERS -->
                <!-- ═══════════════════════════════════════ -->
                <div x-show="assignmentType === 'loose'" x-cloak class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mx-4 sm:mx-0 mb-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">3. Select Loose Meters</h3>

                    @if($looseMeters->count() > 0)
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm text-gray-600">{{ $looseMeters->count() }} ungrouped meters in stock</div>
                            <div class="flex gap-2">
                                <button type="button" @click="selectAllLoose()" class="text-xs text-indigo-600 hover:underline">Select All</button>
                                <button type="button" @click="deselectAllLoose()" class="text-xs text-gray-500 hover:underline">Deselect All</button>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-60 overflow-y-auto">
                            @foreach($looseMeters as $meter)
                                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer transition">
                                    <input type="checkbox" name="loose_meter_ids[]" value="{{ $meter->id }}" x-model="selectedLooseIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-mono text-gray-800">{{ $meter->meter_number }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <span x-text="selectedLooseIds.length"></span> of {{ $looseMeters->count() }} selected
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No ungrouped meters in stock.</p>
                    @endif
                </div>

                <!-- ═══════════════════════════════════════ -->
                <!-- SUBMIT -->
                <!-- ═══════════════════════════════════════ -->
                <div class="mx-4 sm:mx-0 mb-4">
                    <button type="submit" :disabled="!workerId || !canSubmit()"
                        :class="!workerId || !canSubmit() ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                        class="w-full py-3 px-4 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <span x-text="submitLabel()"></span>
                    </button>
                </div>
            </form>

            <!-- ═══════════════════════════════════════ -->
            <!-- RECENT ASSIGNMENTS -->
            <!-- ═══════════════════════════════════════ -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mx-4 sm:mx-0">
                <div class="p-4 sm:p-6">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Recent Assignments</h3>
                    @if($recentAssignments->count() > 0)
                        <!-- Mobile -->
                        <div class="sm:hidden space-y-2">
                            @foreach($recentAssignments as $assign)
                                <div class="flex items-center justify-between border-b border-gray-100 py-2 text-sm">
                                    <div>
                                        <span class="font-mono text-indigo-700">{{ $assign->meter->meter_number }}</span>
                                        <span class="text-gray-400 mx-1">→</span>
                                        <span class="text-gray-700">{{ $assign->worker->name }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $assign->created_at->diffForHumans() }}</div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Desktop -->
                        <table class="hidden sm:table min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Meter</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assigned By</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">When</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentAssignments as $assign)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 font-mono text-indigo-600">
                                            <a href="{{ route('stock.show', $assign->meter) }}" class="hover:underline">{{ $assign->meter->meter_number }}</a>
                                        </td>
                                        <td class="px-3 py-2 text-gray-700">{{ $assign->worker->name }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $assign->assigner->name }}</td>
                                        <td class="px-3 py-2 text-right text-gray-400 text-xs">{{ $assign->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-sm text-gray-500">No assignments yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        function assignmentForm() {
            return {
                workerId: '',
                assignmentType: 'group',
                selectedGroupId: '',
                groupName: '',
                groupMeters: [],
                selectedMeterIds: [],
                selectedLooseIds: [],
                selectedIndividualIds: [],
                individualSearch: '',
                loadingGroup: false,
                looseIds: @json($looseMeters->pluck('id')->map(fn($id) => (string) $id)),
                allIndividualMeters: @json($allStockMeters->map(fn($m) => ['id' => (string) $m->id, 'meter_number' => $m->meter_number, 'group_name' => $m->group ? $m->group->name : null])),

                setType(type) {
                    this.assignmentType = type;
                },

                async loadGroupMeters() {
                    if (!this.selectedGroupId) {
                        this.groupMeters = [];
                        this.selectedMeterIds = [];
                        return;
                    }
                    this.loadingGroup = true;
                    try {
                        const response = await fetch('{{ route("assignments.preview-group") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ group_id: this.selectedGroupId }),
                        });
                        const data = await response.json();
                        this.groupName = data.group_name;
                        this.groupMeters = data.meters;
                        this.selectedMeterIds = data.meters.map(m => String(m.id));
                    } catch (e) {
                        console.error(e);
                    }
                    this.loadingGroup = false;
                },

                filteredIndividualMeters() {
                    if (!this.individualSearch) return this.allIndividualMeters;
                    const q = this.individualSearch.toLowerCase();
                    return this.allIndividualMeters.filter(m => m.meter_number.toLowerCase().includes(q));
                },

                selectAllGroup() {
                    this.selectedMeterIds = this.groupMeters.map(m => String(m.id));
                },
                deselectAllGroup() {
                    this.selectedMeterIds = [];
                },
                selectAllLoose() {
                    this.selectedLooseIds = [...this.looseIds];
                },
                deselectAllLoose() {
                    this.selectedLooseIds = [];
                },
                selectAllIndividual() {
                    const visible = this.filteredIndividualMeters().map(m => m.id);
                    const unique = new Set([...this.selectedIndividualIds, ...visible]);
                    this.selectedIndividualIds = [...unique];
                },
                deselectAllIndividual() {
                    this.selectedIndividualIds = [];
                },

                canSubmit() {
                    if (this.assignmentType === 'group') return this.selectedMeterIds.length > 0;
                    if (this.assignmentType === 'individual') return this.selectedIndividualIds.length > 0;
                    if (this.assignmentType === 'loose') return this.selectedLooseIds.length > 0;
                    return false;
                },

                submitLabel() {
                    if (this.assignmentType === 'group' && this.selectedMeterIds.length > 0) {
                        return 'Assign ' + this.selectedMeterIds.length + ' Meter(s)';
                    }
                    if (this.assignmentType === 'individual' && this.selectedIndividualIds.length > 0) {
                        return 'Assign ' + this.selectedIndividualIds.length + ' Meter(s)';
                    }
                    if (this.assignmentType === 'loose' && this.selectedLooseIds.length > 0) {
                        return 'Assign ' + this.selectedLooseIds.length + ' Loose Meter(s)';
                    }
                    return 'Assign Meters';
                }
            }
        }
    </script>
</x-app-layout>
