<x-admin-layout>
    {{-- Leaflet CSS --}}
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-polydraw@2.0.2/dist/leaflet-polydraw.css" />
    @endpush

    <style>
        /* Override Leaflet z-index so it doesn't conflict with nav dropdowns */
        .leaflet-pane { z-index: 1; }
        .leaflet-top, .leaflet-bottom { z-index: 2; }
        
        .leaflet-control-layers { border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); overflow: hidden;}
        .leaflet-bar { border: none !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important; border-radius: 12px !important; overflow: hidden; }
        .leaflet-bar a { border-bottom: 1px solid #e2e8f0 !important; color: #475569 !important; }
        .leaflet-bar a:hover { background-color: #f8fafc !important; color: #0f172a !important; }

        .layer-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Custom popup styling */
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
        }
        .popup-title {
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 6px;
            color: #0f172a;
        }
        .popup-meta {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }
        
        /* Modern Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <div class="h-full flex flex-col lg:flex-row gap-6">
        {{-- Map Container --}}
        <div class="flex-1 min-h-[500px] bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden relative group">
            <div id="map" class="w-full h-full z-0"></div>
            <!-- Optional subtle overlay shadow inside map -->
            <div class="absolute inset-0 pointer-events-none shadow-inner z-10 rounded-3xl border border-black/5"></div>
        </div>

        {{-- Sidebar (admin only) --}}
        @auth
        <div class="w-full lg:w-[420px] flex flex-col gap-4" id="sidebar">
            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 flex flex-col h-full overflow-hidden">
                
                {{-- Header --}}
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col gap-1">
                    <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-xl shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        </div>
                        Kelola Data Peta
                    </h2>
                    <p class="text-xs font-semibold text-slate-500 ml-[52px]">Atur layer dan tambahkan lokasi baru ke sistem.</p>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                    
                    {{-- Layer Management --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Daftar Layer</h3>
                            <button onclick="openLayerModal()" class="text-[11px] font-bold bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                + LAYER BARU
                            </button>
                        </div>
                        <div id="layer-list" class="space-y-2">
                            <p class="text-sm text-slate-400 italic font-medium">Memuat layer...</p>
                        </div>
                    </div>

                    <div class="w-full h-px bg-slate-100"></div>

                    {{-- Add Item Form --}}
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Input Data Lokasi</h3>
                        <form id="item-form" class="space-y-4">
                            <input type="hidden" id="item-edit-id">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Layer *</label>
                                    <select id="item-layer" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5" required>
                                        <option value="">Pilih layer...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe *</label>
                                    <select id="item-tipe" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5" required>
                                        <option value="marker">Marker (Titik)</option>
                                        <option value="polygon">Polygon (Area)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Judul Lokasi *</label>
                                <input type="text" id="item-judul" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5" placeholder="Masukkan nama tempat..." required>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                                <textarea id="item-deskripsi" rows="3" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5" placeholder="Detail tambahan (opsional)..."></textarea>
                            </div>

                            <div id="marker-fields" class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                <label class="block text-[11px] font-bold text-indigo-800 uppercase tracking-wider mb-2">Koordinat Marker <span class="text-indigo-400 font-normal lowercase">(Klik titik di peta)</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="text" id="item-lat" class="w-full bg-white border border-indigo-100 rounded-lg text-sm text-slate-600 py-2 focus:outline-none cursor-not-allowed" placeholder="Latitude" readonly>
                                    <input type="text" id="item-lng" class="w-full bg-white border border-indigo-100 rounded-lg text-sm text-slate-600 py-2 focus:outline-none cursor-not-allowed" placeholder="Longitude" readonly>
                                </div>
                            </div>

                            <div id="polygon-fields" style="display: none;" class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                                <label class="block text-[11px] font-bold text-emerald-800 uppercase tracking-wider mb-2">Koordinat Polygon</label>
                                <div id="polygon-status" class="text-xs text-emerald-700 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Gunakan toolbar polygon di peta untuk menggambar area.
                                </div>
                                <input type="hidden" id="item-polygon-coords">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kecamatan</label>
                                    <select id="item-kecamatan" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5">
                                        <option value="">Auto-detect...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Input</label>
                                    <input type="date" id="item-tanggal" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5">
                                </div>
                            </div>

                            <button type="submit" id="item-submit-btn" class="w-full bg-indigo-600 text-white py-3.5 px-4 rounded-xl hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-sm font-extrabold flex items-center justify-center gap-2 mt-2">
                                Simpan Data
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>

    {{-- Layer Modal --}}
    <div id="layer-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-gray-500/75" onclick="closeLayerModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md relative">
                <div class="p-6">
                    <h3 id="layer-modal-title" class="text-lg font-semibold text-gray-800 mb-4">Buat Layer Baru</h3>
                    <form id="layer-form" class="space-y-3">
                        <input type="hidden" id="layer-edit-id">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layer *</label>
                            <input type="text" id="layer-nama" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="e.g. Sebaran DBD" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea id="layer-deskripsi" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Deskripsi opsional..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                                <select id="layer-tipe" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="marker">Marker</option>
                                    <option value="polygon">Polygon</option>
                                    <option value="both">Keduanya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna *</label>
                                <input type="color" id="layer-warna" value="#3388ff" class="w-full h-9 border-gray-300 rounded-md shadow-sm cursor-pointer">
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition text-sm font-medium">Simpan</button>
                            <button type="button" onclick="closeLayerModal()" class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition text-sm font-medium">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div id="toast" class="fixed top-20 right-4 z-50 hidden">
        <div class="bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="toast-message"></span>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-polydraw@2.0.2/dist/polydraw.umd.min.js"></script>

    <script>
        // ========== CONFIG ==========
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
        const PADANG_CENTER = [-0.95, 100.35];
        const PADANG_ZOOM = 12;

        // ========== MAP INIT ==========
        const map = L.map('map', {
            center: PADANG_CENTER,
            zoom: PADANG_ZOOM,
            zoomControl: true,
        });

        // Base tile layer
        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        }).addTo(map);

        // ========== LAYER GROUPS ==========
        const kecamatanLayer = L.layerGroup().addTo(map);
        const dynamicLayers = {}; // { layerId: L.layerGroup }
        const layerControl = L.control.layers(null, { 'Batas Kecamatan': kecamatanLayer }, { collapsed: false, position: 'topright' });
        layerControl.addTo(map);

        // Store GeoJSON features for point-in-polygon
        let kecamatanGeoJSON = null;

        // ========== DRAW CONTROL (admin only) ==========
        let polydrawControl = null;
        let currentDrawnLayer = null;

        if (IS_AUTH) {
            polydrawControl = new window.LeafletPolydraw.default();
            map.addControl(polydrawControl);

            function handlePolygonEvent(e) {
                const geojson = e.polygon;
                if (!geojson || !geojson.geometry) return;
                
                const geom = geojson.geometry;
                const rings = geom.type === 'MultiPolygon' ? geom.coordinates[0] : geom.coordinates;
                const coords = rings[0].map(c => [c[1], c[0]]);
                
                document.getElementById('item-polygon-coords').value = JSON.stringify(coords);
                document.getElementById('polygon-status').innerHTML =
                    `<span class="text-green-600 font-medium">✅ Polygon tergambar (${coords.length} titik)</span>`;

                const bounds = L.geoJSON(geojson).getBounds();
                const center = bounds.getCenter();
                autoDetectKecamatan(center.lat, center.lng);
            }

            polydrawControl.on('polydraw:polygon:created', handlePolygonEvent);
            polydrawControl.on('polydraw:polygon:updated', handlePolygonEvent);

            // Handle click on map for marker placement
            map.on('click', function (e) {
                const tipe = document.getElementById('item-tipe').value;
                if (tipe !== 'marker') return;

                document.getElementById('item-lat').value = e.latlng.lat.toFixed(8);
                document.getElementById('item-lng').value = e.latlng.lng.toFixed(8);

                autoDetectKecamatan(e.latlng.lat, e.latlng.lng);

                if (currentDrawnLayer) map.removeLayer(currentDrawnLayer);
                currentDrawnLayer = L.marker(e.latlng).addTo(map);
            });
        }

        // ========== LOAD GEOJSON KECAMATAN ==========
        fetch('/geojson/padang-kecamatan-dissolved.geojson')
            .then(r => r.json())
            .then(data => {
                kecamatanGeoJSON = data;
                L.geoJSON(data, {
                    style: {
                        color: '#1e3a5f',
                        weight: 3,
                        opacity: 1.0,
                        fillColor: '#3b82f6',
                        fillOpacity: 0.15,
                    },
                    onEachFeature: (feature, layer) => {
                        const name = feature.properties.nama_kecamatan || feature.properties.district;
                        layer.bindPopup(`<div class="popup-title">${name}</div><div class="popup-meta">Kecamatan</div>`);
                        layer.on('mouseover', function () {
                            this.setStyle({ fillOpacity: 0.25, weight: 4 });
                        });
                        layer.on('mouseout', function () {
                            this.setStyle({ fillOpacity: 0.15, weight: 3 });
                        });
                    },
                }).addTo(kecamatanLayer);
            });

        // ========== LOAD KECAMATAN DROPDOWN ==========
        fetch('/api/kecamatan')
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('item-kecamatan');
                if (!select) return;
                data.forEach(k => {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = k.nama_kecamatan;
                    select.appendChild(opt);
                });
            });

        // ========== POINT-IN-POLYGON ==========
        function pointInPolygon(lat, lng, polygon) {
            // Ray casting algorithm
            let inside = false;
            for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                const xi = polygon[i][0], yi = polygon[i][1];
                const xj = polygon[j][0], yj = polygon[j][1];
                const intersect = ((yi > lng) !== (yj > lng)) &&
                    (lat < (xj - xi) * (lng - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }

        function autoDetectKecamatan(lat, lng) {
            if (!kecamatanGeoJSON) return;
            const select = document.getElementById('item-kecamatan');
            if (!select) return;

            for (const feature of kecamatanGeoJSON.features) {
                const geomType = feature.geometry.type;
                let polygons = [];

                if (geomType === 'Polygon') {
                    polygons = feature.geometry.coordinates;
                } else if (geomType === 'MultiPolygon') {
                    feature.geometry.coordinates.forEach(mp => {
                        polygons.push(...mp);
                    });
                }

                for (const ring of polygons) {
                    // GeoJSON is [lng, lat], our function expects [lng, lat] for x, lat for y
                    if (pointInPolygon(lng, lat, ring)) {
                        const name = feature.properties.nama_kecamatan;
                        // Find matching option
                        for (const opt of select.options) {
                            if (opt.textContent === name) {
                                select.value = opt.value;
                                return;
                            }
                        }
                    }
                }
            }
        }

        // ========== LOAD LAYERS & ITEMS ==========
        function loadLayers() {
            fetch('/api/layers')
                .then(r => r.json())
                .then(layers => {
                    window.cachedLayers = layers; // Save for editing items
                    renderLayerList(layers);
                    renderLayerSelect(layers);
                    renderMapLayers(layers);
                });
        }

        function renderLayerList(layers) {
            const container = document.getElementById('layer-list');
            if (!container) return;

            if (layers.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 italic">Belum ada layer. Buat layer baru untuk mulai.</p>';
                return;
            }

            container.innerHTML = layers.map(l => `
                <div class="flex items-center justify-between py-2 px-3 rounded-md hover:bg-gray-50 group border border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="layer-dot" style="background:${l.warna}"></span>
                        <span class="text-sm font-medium text-gray-700">${l.nama}</span>
                        <span class="text-xs text-gray-400">(${l.items.length})</span>
                    </div>
                    <div class="flex gap-2 transition">
                        <button onclick="editLayer(${l.id})" class="text-xs font-bold text-indigo-500 hover:text-indigo-700" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button onclick="deleteLayer(${l.id})" class="text-xs font-bold text-red-500 hover:text-red-700" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function renderLayerSelect(layers) {
            const select = document.getElementById('item-layer');
            if (!select) return;

            const currentVal = select.value;
            select.innerHTML = '<option value="">Pilih layer...</option>';
            layers.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id;
                opt.textContent = `${l.nama} (${l.tipe})`;
                opt.dataset.tipe = l.tipe;
                select.appendChild(opt);
            });
            if (currentVal) select.value = currentVal;
        }

        function renderMapLayers(layers) {
            // Remove existing dynamic layers from map and control
            Object.keys(dynamicLayers).forEach(id => {
                map.removeLayer(dynamicLayers[id]);
                layerControl.removeLayer(dynamicLayers[id]);
                delete dynamicLayers[id];
            });

            layers.forEach(layer => {
                const group = L.layerGroup().addTo(map);
                dynamicLayers[layer.id] = group;

                layer.items.forEach(item => {
                    if (item.tipe === 'marker' && item.latitude && item.longitude) {
                        const marker = L.circleMarker([item.latitude, item.longitude], {
                            radius: 8,
                            fillColor: layer.warna,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.85,
                        });

                        let popupHtml = `<div class="popup-title">${item.judul}</div>`;
                        if (item.deskripsi) popupHtml += `<div class="popup-meta">${item.deskripsi}</div>`;
                        if (item.kecamatan) popupHtml += `<div class="popup-meta flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ${item.kecamatan.nama_kecamatan}</div>`;
                        if (item.tanggal) popupHtml += `<div class="popup-meta flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> ${item.tanggal.split('T')[0]}</div>`;
                        if (IS_AUTH) {
                            popupHtml += `<div class="popup-actions mt-2 flex gap-2">
                                <button class="flex-1 text-center text-xs font-bold text-white bg-indigo-500 hover:bg-indigo-600 py-1.5 rounded-md flex items-center justify-center gap-1" onclick="editItem(${item.id})">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                                <button class="flex-1 text-center text-xs font-bold text-white bg-red-500 hover:bg-red-600 py-1.5 rounded-md flex items-center justify-center gap-1" onclick="deleteItem(${item.id})">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>`;
                        }
                        marker.bindPopup(popupHtml);
                        marker.addTo(group);

                    } else if (item.tipe === 'polygon' && item.polygon_coords) {
                        const coords = item.polygon_coords.map(c => [c[0], c[1]]);
                        const polygon = L.polygon(coords, {
                            color: layer.warna,
                            weight: 2,
                            opacity: 0.8,
                            fillColor: layer.warna,
                            fillOpacity: 0.25,
                        });

                        let popupHtml = `<div class="popup-title">${item.judul}</div>`;
                        if (item.deskripsi) popupHtml += `<div class="popup-meta">${item.deskripsi}</div>`;
                        if (item.kecamatan) popupHtml += `<div class="popup-meta flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ${item.kecamatan.nama_kecamatan}</div>`;
                        if (IS_AUTH) {
                            popupHtml += `<div class="popup-actions mt-2 flex gap-2">
                                <button class="flex-1 text-center text-xs font-bold text-white bg-indigo-500 hover:bg-indigo-600 py-1.5 rounded-md flex items-center justify-center gap-1" onclick="editItem(${item.id})">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                                <button class="flex-1 text-center text-xs font-bold text-white bg-red-500 hover:bg-red-600 py-1.5 rounded-md flex items-center justify-center gap-1" onclick="deleteItem(${item.id})">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>`;
                        }
                        polygon.bindPopup(popupHtml);
                        polygon.addTo(group);
                    }
                });

                // Add to layer control with colored dot
                const label = `<span class="layer-dot" style="background:${layer.warna}"></span> ${layer.nama}`;
                layerControl.addOverlay(group, label);
            });
        }

        // ========== LAYER CRUD ==========
        function openLayerModal(editData = null) {
            document.getElementById('layer-modal').classList.remove('hidden');
            if (editData) {
                document.getElementById('layer-modal-title').textContent = 'Edit Layer';
                document.getElementById('layer-edit-id').value = editData.id;
                document.getElementById('layer-nama').value = editData.nama;
                document.getElementById('layer-deskripsi').value = editData.deskripsi || '';
                document.getElementById('layer-tipe').value = editData.tipe;
                document.getElementById('layer-warna').value = editData.warna;
            } else {
                document.getElementById('layer-modal-title').textContent = 'Buat Layer Baru';
                document.getElementById('layer-edit-id').value = '';
                document.getElementById('layer-form').reset();
                document.getElementById('layer-warna').value = '#3388ff';
            }
        }

        function closeLayerModal() {
            document.getElementById('layer-modal').classList.add('hidden');
        }

        function editLayer(id) {
            fetch('/api/layers')
                .then(r => r.json())
                .then(layers => {
                    const layer = layers.find(l => l.id === id);
                    if (layer) openLayerModal(layer);
                });
        }

        function deleteLayer(id) {
            if (!confirm('Hapus layer ini beserta semua item-nya?')) return;
            fetch(`/layers/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            })
            .then(r => {
                if (!r.ok) throw new Error('Gagal menghapus layer (Server Error)');
                return r.json();
            })
            .then(() => {
                showToast('Layer berhasil dihapus');
                loadLayers();
            })
            .catch(err => alert('Error: ' + err.message));
        }

        document.getElementById('layer-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const editId = document.getElementById('layer-edit-id').value;
            const data = {
                nama: document.getElementById('layer-nama').value,
                deskripsi: document.getElementById('layer-deskripsi').value,
                tipe: document.getElementById('layer-tipe').value,
                warna: document.getElementById('layer-warna').value,
            };

            const url = editId ? `/layers/${editId}` : '/layers';
            const method = editId ? 'PUT' : 'POST';

            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(r => r.json())
            .then(() => {
                closeLayerModal();
                showToast(editId ? 'Layer berhasil diupdate' : 'Layer berhasil dibuat');
                loadLayers();
            })
            .catch(err => alert('Error: ' + err.message));
        });

        // ========== ITEM CRUD ==========
        function toggleDrawToolbar() {
            const tipeEl = document.getElementById('item-tipe');
            if (!tipeEl) return;
            const isPolygon = tipeEl.value === 'polygon';
            document.getElementById('marker-fields').style.display = isPolygon ? 'none' : 'block';
            document.getElementById('polygon-fields').style.display = isPolygon ? 'block' : 'none';
            if (typeof polydrawControl !== 'undefined' && polydrawControl && polydrawControl.getContainer()) {
                polydrawControl.getContainer().style.display = isPolygon ? 'block' : 'none';
            }
        }

        document.getElementById('item-tipe')?.addEventListener('change', toggleDrawToolbar);

        document.getElementById('item-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('item-submit-btn');
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            const tipe = document.getElementById('item-tipe').value;
            const data = {
                map_layer_id: document.getElementById('item-layer').value,
                tipe: tipe,
                judul: document.getElementById('item-judul').value,
                deskripsi: document.getElementById('item-deskripsi').value,
                kecamatan_id: document.getElementById('item-kecamatan').value || null,
                tanggal: document.getElementById('item-tanggal').value || null,
            };

            if (tipe === 'marker') {
                data.latitude = parseFloat(document.getElementById('item-lat').value);
                data.longitude = parseFloat(document.getElementById('item-lng').value);
                if (!data.latitude || !data.longitude) {
                    alert('Klik peta untuk menentukan koordinat terlebih dahulu.');
                    btn.disabled = false;
                    btn.textContent = 'Simpan Item';
                    return;
                }
            } else {
                const polyCoords = document.getElementById('item-polygon-coords').value;
                if (!polyCoords) {
                    alert('Gambar polygon di peta terlebih dahulu menggunakan toolbar.');
                    btn.disabled = false;
                    btn.textContent = 'Simpan Item';
                    return;
                }
                data.polygon_coords = JSON.parse(polyCoords);
            }

            const editId = document.getElementById('item-edit-id').value;
            const url = editId ? `/items/${editId}` : '/items';
            const method = editId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(r => {
                if (!r.ok) return r.json().then(err => { throw err; });
                return r.json();
            })
            .then(() => {
                showToast(editId ? 'Item berhasil diupdate' : 'Item berhasil ditambahkan');
                resetItemForm();
                loadLayers();
            })
            .catch(err => {
                if (err.errors) {
                    alert('Validasi error:\n' + Object.values(err.errors).flat().join('\n'));
                } else {
                    alert('Error: ' + (err.message || 'Terjadi kesalahan'));
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Simpan Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            });
        });

        function editItem(id) {
            let foundItem = null;
            if (window.cachedLayers) {
                window.cachedLayers.forEach(l => {
                    const i = l.items.find(x => x.id === id);
                    if (i) foundItem = i;
                });
            }
            if (!foundItem) return;

            document.getElementById('item-edit-id').value = foundItem.id;
            document.getElementById('item-layer').value = foundItem.map_layer_id;
            document.getElementById('item-tipe').value = foundItem.tipe;
            document.getElementById('item-judul').value = foundItem.judul;
            document.getElementById('item-deskripsi').value = foundItem.deskripsi || '';
            document.getElementById('item-kecamatan').value = foundItem.kecamatan_id || '';
            if (foundItem.tanggal) document.getElementById('item-tanggal').value = foundItem.tanggal.split('T')[0];

            if (foundItem.tipe === 'marker') {
                document.getElementById('item-lat').value = foundItem.latitude;
                document.getElementById('item-lng').value = foundItem.longitude;
            } else {
                document.getElementById('item-polygon-coords').value = JSON.stringify(foundItem.polygon_coords);
                document.getElementById('polygon-status').innerHTML = 'Polygon sudah ada. Gambar ulang di peta untuk mengubah.';
            }
            toggleDrawToolbar();

            document.getElementById('item-submit-btn').innerHTML = 'Update Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            
            // Pan to item and show edit marker if possible
            if (foundItem.tipe === 'marker' && foundItem.latitude) {
                map.setView([foundItem.latitude, foundItem.longitude], 15);
                if (currentDrawnLayer) map.removeLayer(currentDrawnLayer);
                currentDrawnLayer = L.marker([foundItem.latitude, foundItem.longitude]).addTo(map);
            }
        }

        function deleteItem(id) {
            if (!confirm('Hapus item ini?')) return;
            fetch(`/items/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            })
            .then(r => {
                if (!r.ok) throw new Error('Gagal menghapus item (Server Error)');
                return r.json();
            })
            .then(() => {
                showToast('Item berhasil dihapus');
                map.closePopup();
                loadLayers();
            })
            .catch(err => alert('Error: ' + err.message));
        }

        function resetItemForm() {
            document.getElementById('item-edit-id').value = '';
            document.getElementById('item-form').reset();
            document.getElementById('item-submit-btn').innerHTML = 'Simpan Data <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            document.getElementById('item-lat').value = '';
            document.getElementById('item-lng').value = '';
            document.getElementById('item-polygon-coords').value = '';
            document.getElementById('polygon-status').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Gunakan toolbar polygon di peta untuk menggambar area.';
            document.getElementById('item-tipe').value = 'marker';
            toggleDrawToolbar();
            if (currentDrawnLayer) {
                map.removeLayer(currentDrawnLayer);
                currentDrawnLayer = null;
            }
            if (typeof polydrawControl !== 'undefined' && polydrawControl) {
                polydrawControl.removeAllFeatureGroups();
            }
        }

        // ========== TOAST ==========
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        // ========== INIT ==========
        loadLayers();
        setTimeout(toggleDrawToolbar, 100); // Initialize toolbar visibility
    </script>
</x-admin-layout>
