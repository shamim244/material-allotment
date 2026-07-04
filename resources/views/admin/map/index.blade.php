<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-black leading-tight flex items-center gap-2">
            <span>🗺️</span> {{ __('Installation Map') }}
        </h2>
    </x-slot>

    <!-- Stylesheets for Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <div class="py-6 bg-[#f8fafc] min-h-screen text-black relative overflow-hidden">
        <!-- Background Blur Accents -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-50 rounded-full blur-[120px] -translate-y-1/3 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-slate-100 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-6">
            
            <!-- Filter Bar & Search Card -->
            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-6">
                <form method="GET" action="{{ route('admin.map.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="worker_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Worker</label>
                        <select name="worker_id" id="worker_id" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-sm py-2">
                            <option value="">All Workers</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="install_type" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Type</label>
                        <select name="install_type" id="install_type" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-sm py-2">
                            <option value="">All Types</option>
                            <option value="nsc" {{ request('install_type') == 'nsc' ? 'selected' : '' }}>NSC (New Service Connection)</option>
                            <option value="replacement" {{ request('install_type') == 'replacement' ? 'selected' : '' }}>Replacement</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="date_from" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">From</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-sm py-2">
                        </div>
                        <div>
                            <label for="date_to" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">To</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full rounded-xl border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black text-sm py-2">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all">
                            Filter
                        </button>
                        @if(request()->anyFilled(['worker_id', 'install_type', 'date_from', 'date_to']))
                            <a href="{{ route('admin.map.index') }}" class="inline-flex justify-center items-center p-2.5 rounded-xl text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-all" title="Clear Filters">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Map & Sidebar Layout -->
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- Sidebar: Installations List -->
                <div class="w-full lg:w-1/3 bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl p-6 flex flex-col h-[600px]">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold mb-1">Installations ({{ count($installations) }})</h3>
                        <p class="text-xs text-gray-500 mb-3">Filter results instantly using the search box below.</p>
                        
                        <!-- Client-side Search -->
                        <div class="relative">
                            <svg class="absolute left-3.5 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="sidebarSearch" placeholder="Search meter or CA number..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 bg-white/50 backdrop-blur-sm focus:border-black focus:ring-black rounded-xl transition-all">
                        </div>
                    </div>

                    <!-- Scrollable list -->
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1" id="installationsList">
                        @forelse($installations as $inst)
                            <div class="p-3 bg-white/85 rounded-2xl border border-gray-100 shadow-[0_2px_8px_rgb(0,0,0,0.02)] hover:border-black/20 hover:shadow-md transition-all cursor-pointer installation-card" 
                                 data-id="{{ $inst['id'] }}"
                                 data-lat="{{ $inst['latitude'] }}"
                                 data-lng="{{ $inst['longitude'] }}"
                                 data-meter="{{ $inst['meter_number'] }}"
                                 data-ca="{{ $inst['ca_number'] }}">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-sm text-indigo-700"># {{ $inst['meter_number'] }}</span>
                                    @if($inst['install_type'] === 'nsc')
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wide rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase">NSC</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wide rounded-full bg-amber-50 text-amber-700 border border-amber-100 uppercase">Replacement</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-600 space-y-1 mt-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Worker:</span>
                                        <span class="font-medium text-gray-800">{{ $inst['worker_name'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">CA Number:</span>
                                        <span class="font-medium text-gray-800">{{ $inst['ca_number'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Date:</span>
                                        <span class="font-medium text-gray-800">{{ $inst['created_at'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 text-sm">
                                <span class="text-3xl block mb-2">🤷‍♂️</span>
                                No installations found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Main Content: Map Container -->
                <div class="flex-1 bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-3xl overflow-hidden h-[600px] relative">
                    <div id="map" class="w-full h-full z-10"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- Leaflet & Clustering scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load installations data
            const installations = @json($installations);

            if (installations.length === 0) {
                // If empty, center on default coordinate (e.g. India center)
                const map = L.map('map').setView([20.5937, 78.9629], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                L.popup()
                    .setLatLng([20.5937, 78.9629])
                    .setContent('<div class="p-2 text-center font-bold text-sm text-gray-700">No coordinate data available.</div>')
                    .openOn(map);
                return;
            }

            // Calculate center of coordinates
            let totalLat = 0;
            let totalLng = 0;
            installations.forEach(inst => {
                totalLat += inst.latitude;
                totalLng += inst.longitude;
            });
            const centerLat = totalLat / installations.length;
            const centerLng = totalLng / installations.length;

            // Initialize Map
            const map = L.map('map').setView([centerLat, centerLng], 12);

            // Add OpenStreetMap Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Custom Div Icons for Map Pins
            const nscIcon = L.divIcon({
                html: `<div class="w-8 h-8 rounded-full bg-indigo-500 border-2 border-white flex items-center justify-center text-white shadow-lg font-bold text-xs">🆕</div>`,
                className: 'custom-div-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const replacementIcon = L.divIcon({
                html: `<div class="w-8 h-8 rounded-full bg-amber-500 border-2 border-white flex items-center justify-center text-white shadow-lg font-bold text-xs">🔄</div>`,
                className: 'custom-div-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            // Initialize Marker Cluster Group
            const markerCluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                maxClusterRadius: 40
            });

            // Keep track of markers by ID for dynamic zooming/opening
            const markersMap = {};

            // Plot all installation markers
            installations.forEach(inst => {
                const icon = inst.install_type === 'nsc' ? nscIcon : replacementIcon;
                const marker = L.marker([inst.latitude, inst.longitude], { icon: icon });

                // Construct Premium Popup
                const popupContent = `
                    <div class="p-3 font-sans min-w-[200px]">
                        <div class="flex justify-between items-center mb-2 pb-1.5 border-b border-gray-100">
                            <a href="/stock/${inst.meter_id}" class="font-extrabold text-sm text-indigo-600 hover:underline">
                                Meter #${inst.meter_number}
                            </a>
                            <span class="px-1.5 py-0.5 text-[8px] font-extrabold rounded bg-gray-100 text-gray-800 uppercase">
                                ${inst.install_type}
                            </span>
                        </div>
                        <div class="space-y-1 text-xs">
                            <div><strong class="text-gray-500 font-medium">Technician:</strong> <span class="font-semibold text-gray-800">${inst.worker_name}</span></div>
                            <div><strong class="text-gray-500 font-medium">CA Number:</strong> <span class="font-semibold text-gray-800">${inst.ca_number}</span></div>
                            <div><strong class="text-gray-500 font-medium">Old Meter:</strong> <span class="font-semibold text-gray-800">${inst.old_meter_number}</span></div>
                            <div><strong class="text-gray-500 font-medium">Installed:</strong> <span class="font-semibold text-gray-800">${inst.created_at}</span></div>
                            ${inst.remark ? `<div class="mt-1.5 p-1 bg-gray-50 rounded text-gray-600 text-[10px] italic">"${inst.remark}"</div>` : ''}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markerCluster.addLayer(marker);
                markersMap[inst.id] = marker;
            });

            map.addLayer(markerCluster);

            // Zoom map to fit all markers nicely
            map.fitBounds(markerCluster.getBounds(), { padding: [50, 50] });

            // Sidebar List Interactivity
            const cards = document.querySelectorAll('.installation-card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const lat = parseFloat(this.getAttribute('data-lat'));
                    const lng = parseFloat(this.getAttribute('data-lng'));
                    const marker = markersMap[id];

                    if (marker) {
                        // Fly to marker coordinates
                        map.flyTo([lat, lng], 16, {
                            animate: true,
                            duration: 1.2
                        });

                        // Wait for fly animation to complete, then zoom/open popup
                        setTimeout(() => {
                            // If marker is in a cluster, Leaflet markercluster has helper to zoom to it
                            markerCluster.zoomToShowLayer(marker, () => {
                                marker.openPopup();
                            });
                        }, 600);
                    }
                });
            });

            // Client-side instant search filtering
            const searchInput = document.getElementById('sidebarSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.installation-card');
                    
                    // Clear clusters
                    markerCluster.clearLayers();

                    cards.forEach(card => {
                        const meterNum = card.getAttribute('data-meter').toLowerCase();
                        const caNum = card.getAttribute('data-ca').toLowerCase();
                        const id = card.getAttribute('data-id');
                        const marker = markersMap[id];

                        if (meterNum.includes(query) || caNum.includes(query)) {
                            // Show card
                            card.classList.remove('hidden');
                            // Add marker back
                            if (marker) {
                                markerCluster.addLayer(marker);
                            }
                        } else {
                            // Hide card
                            card.classList.add('hidden');
                        }
                    });
                });
            }
        });
    </script>

    <!-- Custom inline popup styling overrides for a premium feel -->
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 1.25rem !important;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
        }
        .leaflet-popup-tip {
            background: rgba(255, 255, 255, 0.95) !important;
        }
        .custom-div-icon {
            background: transparent !important;
            border: none !important;
        }
    </style>
</x-app-layout>
