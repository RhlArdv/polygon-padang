<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sebaran Kasus Kota Padang</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Figtree', sans-serif; }
        
        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Leaflet Popups */
        .leaflet-popup-content-wrapper {
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 0;
            overflow: hidden;
        }
        .leaflet-popup-content { margin: 0; width: 260px !important; }
        .leaflet-popup-close-button {
            top: 10px !important; right: 10px !important;
            color: #fff !important; background: rgba(0,0,0,0.4) !important;
            border-radius: 99px; padding: 2px; height: 20px !important; width: 20px !important;
            line-height: 16px !important; text-align: center;
        }
        .leaflet-popup-close-button:hover { background: rgba(0,0,0,0.7) !important; }

        /* Custom Tooltip */
        .custom-tooltip {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            font-weight: 700;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
        }
        .leaflet-tooltip-top:before { border-top-color: rgba(15, 23, 42, 0.85); }
    </style>
</head>
<body class="antialiased bg-slate-50 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Navbar (Glassmorphism) -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/70 backdrop-blur-md border-b border-slate-200/50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Kota Padang" class="h-10 w-auto">
                    <div class="flex flex-col">
                        <span class="text-sm md:text-base font-extrabold text-slate-900 leading-tight tracking-tight">SIG KOTA PADANG</span>
                        <span class="text-[10px] md:text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemetaan Terintegrasi</span>
                    </div>
                </div>

                <!-- Navigation/Login Links -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-4 py-2 rounded-xl">Masuk Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 px-4 py-2 rounded-xl transition-all shadow-md hover:shadow-lg">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-24 pb-8 flex flex-col gap-6">
        
        <!-- Header Section -->
        <div class="text-center max-w-2xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Peta Sebaran Kasus & Lokasi Strategis</h1>
            <p class="text-sm md:text-base text-slate-500 font-medium">Platform informasi geografis resmi Kota Padang untuk memantau sebaran data dan zonasi wilayah secara real-time.</p>
        </div>

        <!-- Map Container (Glassmorphism + Rounded) -->
        <div class="relative w-full h-[65vh] min-h-[500px] rounded-3xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.12)] border border-slate-200/60 bg-white">
            <div id="map"></div>
            
            <!-- Layer Control Panel (Custom styled) -->
            <div class="absolute top-4 right-4 z-[400] w-64 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/50 overflow-hidden" x-data="{ open: true }">
                <div class="p-3 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-slate-50" @click="open = !open">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Filter Layer
                    </h3>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                
                <div x-show="open" x-transition class="p-3 max-h-[300px] overflow-y-auto" id="custom-layer-list">
                    <!-- Layer checkboxes will be populated here via JS -->
                    <div class="flex items-center justify-center p-4">
                        <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs font-semibold text-slate-500">&copy; {{ date('Y') }} Pemerintah Kota Padang. All rights reserved.</p>
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-400">
                <a href="#" class="hover:text-slate-700 transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-slate-700 transition">Syarat Ketentuan</a>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const PADANG_CENTER = [-0.95, 100.35];
            
            const map = L.map('map', {
                center: PADANG_CENTER,
                zoom: 12,
                zoomControl: false, // We'll position it manually
                preferCanvas: true
            });

            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google Maps'
            }).addTo(map);

            // ==========================================
            // KECAMATAN LAYER (BASE)
            // ==========================================
            const kecamatanLayer = L.layerGroup().addTo(map);
            let kecamatanGeoJSON = null;

            function getKecamatanColor(name) {
                const cleanName = (name || '').toLowerCase().replace('kecamatan', '').replace('kec.', '').trim();
                if (cleanName.includes('barat')) return '#6366f1';
                if (cleanName.includes('timur')) return '#3b82f6';
                if (cleanName.includes('utara')) return '#14b8a6';
                if (cleanName.includes('selatan')) return '#f59e0b';
                if (cleanName.includes('kuranji')) return '#10b981';
                if (cleanName.includes('tangah')) return '#8b5cf6';
                if (cleanName.includes('nanggalo')) return '#ec4899';
                if (cleanName.includes('begalung')) return '#06b6d4';
                if (cleanName.includes('kilangan')) return '#84cc16';
                if (cleanName.includes('pauh')) return '#f97316';
                if (cleanName.includes('bungus') || cleanName.includes('kabung')) return '#ef4444';
                return '#64748b';
            }

            fetch('/geojson/padang-kecamatan-dissolved.geojson')
                .then(r => r.json())
                .then(data => {
                    kecamatanGeoJSON = data;
                    L.geoJSON(data, {
                        style: function(feature) {
                            const name = feature.properties.nama_kecamatan || feature.properties.district || '';
                            const color = getKecamatanColor(name);
                            return { color: color, weight: 3, opacity: 1, fillColor: color, fillOpacity: 0.15 };
                        },
                        onEachFeature: function(feature, layer) {
                            const name = feature.properties.nama_kecamatan || feature.properties.district || 'Kecamatan';
                            const color = getKecamatanColor(name);
                            
                            layer.on({
                                mouseover: e => {
                                    e.target.setStyle({ fillOpacity: 0.25, weight: 4, color: color });
                                    e.target.bringToBack();
                                },
                                mouseout: e => {
                                    e.target.setStyle({ fillOpacity: 0.15, weight: 3, color: color });
                                }
                            });
                            
                            layer.bindTooltip(name, { className: 'custom-tooltip', direction: 'center' });
                        }
                    }).addTo(kecamatanLayer);
                });

            // ==========================================
            // DYNAMIC LAYERS
            // ==========================================
            const dynamicLayers = {}; 
            
            function buildPopup(item, layerData) {
                return `
                    <div class="flex flex-col">
                        <div class="p-4" style="background: linear-gradient(135deg, ${layerData.warna}15, ${layerData.warna}30)">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white shadow-sm mb-3">
                                <svg class="w-6 h-6" style="color: ${layerData.warna}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-800 leading-tight">${item.judul}</h4>
                            <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-bold text-white rounded-md" style="background-color: ${layerData.warna}">
                                ${layerData.nama}
                            </span>
                        </div>
                        <div class="p-4 bg-white flex flex-col gap-2">
                            <p class="text-xs text-slate-600">${item.deskripsi || 'Tidak ada deskripsi'}</p>
                            ${item.kecamatan ? `
                                <div class="flex items-center gap-1.5 mt-2 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Kecamatan ${item.kecamatan.nama_kecamatan}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            // Load data from API
            fetch('/api/layers')
                .then(r => r.json())
                .then(layers => {
                    const listContainer = document.getElementById('custom-layer-list');
                    listContainer.innerHTML = '';
                    
                    // Add Base Kecamatan checkbox
                    const baseLayerHtml = `
                        <label class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 cursor-pointer group transition">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                <span class="text-xs font-bold text-slate-700">Batas Kecamatan</span>
                            </div>
                            <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20" onchange="if(this.checked) map.addLayer(kecamatanLayer); else map.removeLayer(kecamatanLayer);">
                        </label>
                    `;
                    listContainer.insertAdjacentHTML('beforeend', baseLayerHtml);

                    // Add dynamic layers
                    layers.forEach(l => {
                        const lg = L.layerGroup();
                        if (l.is_active) lg.addTo(map);
                        dynamicLayers[l.id] = lg;

                        l.items.forEach(item => {
                            let leafletObj;
                            if (item.tipe === 'marker') {
                                const customIcon = L.divIcon({
                                    className: 'custom-marker',
                                    html: `
                                        <div class="relative flex items-center justify-center w-8 h-8 -mt-4 -ml-4 transition-transform hover:scale-110 drop-shadow-md">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="absolute w-full h-full">
                                                <path d="M12 21.5C12 21.5 20.5 15.79 20.5 9.5C20.5 4.80558 16.6944 1 12 1C7.30558 1 3.5 4.80558 3.5 9.5C3.5 15.79 12 21.5 12 21.5Z" fill="${l.warna}" stroke="white" stroke-width="2"/>
                                                <circle cx="12" cy="9.5" r="4.5" fill="white"/>
                                            </svg>
                                        </div>
                                    `,
                                    iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -28]
                                });
                                leafletObj = L.marker([item.latitude, item.longitude], { icon: customIcon });
                            } else {
                                leafletObj = L.polygon(item.polygon_coords, {
                                    color: l.warna, weight: 3, opacity: 0.8, fillColor: l.warna, fillOpacity: 0.4
                                });
                            }

                            leafletObj.bindPopup(buildPopup(item, l));
                            leafletObj.addTo(lg);
                        });

                        // Add to UI
                        const checkedAttr = l.is_active ? 'checked' : '';
                        const html = `
                            <label class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 cursor-pointer group transition border-t border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full shadow-sm" style="background-color: ${l.warna}"></span>
                                    <span class="text-xs font-bold text-slate-700 truncate w-[130px]">${l.nama}</span>
                                </div>
                                <input type="checkbox" ${checkedAttr} value="${l.id}" 
                                    class="dynamic-layer-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                            </label>
                        `;
                        listContainer.insertAdjacentHTML('beforeend', html);
                    });

                    // Checkbox event listeners
                    document.querySelectorAll('.dynamic-layer-checkbox').forEach(cb => {
                        cb.addEventListener('change', function() {
                            const layerId = this.value;
                            if (this.checked) map.addLayer(dynamicLayers[layerId]);
                            else map.removeLayer(dynamicLayers[layerId]);
                        });
                    });
                });
        });
    </script>
</body>
</html>
