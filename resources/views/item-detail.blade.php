<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mapItem->judul }} - Padang GIS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Leaflet CSS for small map preview -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-md group-hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </div>
                <span class="font-extrabold text-slate-800 group-hover:text-indigo-600 transition tracking-tight">Kembali ke Peta</span>
            </a>
            
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm" style="background-color: {{ $mapItem->mapLayer->warna }}">
                {{ $mapItem->mapLayer->nama }}
            </span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden">
            
            <!-- Hero Image -->
            @if($mapItem->gambar)
                <div class="w-full h-64 md:h-96 relative">
                    <img src="{{ asset('storage/' . $mapItem->gambar) }}" alt="{{ $mapItem->judul }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
            @else
                <div class="w-full h-32 md:h-48 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
                    <div class="absolute inset-0 bg-white/10" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 20px 20px;"></div>
                </div>
            @endif

            <div class="p-6 md:p-10 relative -mt-16 md:-mt-24 z-10">
                <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 inline-block max-w-3xl border border-slate-100">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">{{ $mapItem->judul }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-500 mb-6">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $mapItem->tanggal ? \Carbon\Carbon::parse($mapItem->tanggal)->translatedFormat('d F Y') : 'Tanggal tidak tersedia' }}
                        </div>
                        <div class="flex items-center gap-1.5" id="kecamatan-container">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span id="kecamatan-text" class="italic text-slate-400">Mendeteksi lokasi...</span>
                        </div>
                    </div>

                    <div class="prose prose-slate prose-indigo max-w-none">
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Deskripsi</h3>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $mapItem->deskripsi ?: 'Tidak ada deskripsi rinci untuk data ini.' }}</p>
                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Lokasi pada Peta
                    </h3>
                    <div id="detail-map" class="w-full h-[400px] rounded-2xl shadow-inner border border-slate-200 z-0 relative"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-semibold text-slate-500">&copy; {{ date('Y') }} Pemerintah Kota Padang. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapData = @json($mapItem);
            
            const map = L.map('detail-map', {
                zoomControl: true,
                preferCanvas: true
            });
            
            L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google Maps'
            }).addTo(map);

            let layer;
            if (mapData.tipe === 'marker') {
                layer = L.marker([mapData.latitude, mapData.longitude]).addTo(map);
                map.setView([mapData.latitude, mapData.longitude], 15);
            } else if (mapData.tipe === 'polygon') {
                layer = L.polygon(mapData.polygon_coords, {
                    color: mapData.map_layer.warna || '#3b82f6',
                    weight: 3,
                    fillOpacity: 0.4
                }).addTo(map);
                map.fitBounds(layer.getBounds());
            }

            // Detect kecamatans dynamically
            function pointInPolygon(lat, lng, polygon) {
                let inside = false;
                for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                    const xi = polygon[i][0], yi = polygon[i][1];
                    const xj = polygon[j][0], yj = polygon[j][1];
                    const intersect = ((yi > lng) !== (yj > lng)) && (lat < (xj - xi) * (lng - yi) / (yj - yi) + xi);
                    if (intersect) inside = !inside;
                }
                return inside;
            }

            fetch('/geojson/padang-kecamatan-dissolved.geojson')
                .then(r => r.json())
                .then(kecamatanGeoJSON => {
                    let matchedNames = [];
                    const lat = mapData.latitude;
                    const lng = mapData.longitude;
                    
                    if (mapData.tipe === 'polygon' && mapData.polygon_coords) {
                        const itemPolygons = mapData.polygon_coords;
                        
                        kecamatanGeoJSON.features.forEach(feature => {
                            const geom = feature.geometry;
                            const rings = geom.type === 'MultiPolygon' ? geom.coordinates[0] : geom.coordinates;
                            const polyCoords = rings[0].map(c => [c[1], c[0]]); // [lat, lng]
                            
                            for (const itemRing of itemPolygons) {
                                for (const point of itemRing) {
                                    if (pointInPolygon(point[0], point[1], polyCoords)) {
                                        matchedNames.push(feature.properties.nama_kecamatan || feature.properties.district);
                                        break; // Next feature
                                    }
                                }
                                if (matchedNames.includes(feature.properties.nama_kecamatan || feature.properties.district)) break;
                            }
                        });
                    } else if (lat !== null && lng !== null) {
                        kecamatanGeoJSON.features.forEach(feature => {
                            const geom = feature.geometry;
                            const rings = geom.type === 'MultiPolygon' ? geom.coordinates[0] : geom.coordinates;
                            const polyCoords = rings[0].map(c => [c[1], c[0]]);
                            if (pointInPolygon(lat, lng, polyCoords)) {
                                matchedNames.push(feature.properties.nama_kecamatan || feature.properties.district);
                            }
                        });
                    }

                    const kText = document.getElementById('kecamatan-text');
                    kText.classList.remove('italic', 'text-slate-400');
                    if (matchedNames.length > 0) {
                        kText.textContent = matchedNames.join(', ');
                    } else {
                        kText.textContent = 'Tidak diketahui';
                    }
                })
                .catch(err => {
                    console.error('Failed to load kecamatan geojson', err);
                    document.getElementById('kecamatan-text').textContent = 'Tidak diketahui';
                });
        });
    </script>
</body>
</html>
