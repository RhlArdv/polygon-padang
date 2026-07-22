<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center justify-center w-14 h-14 bg-white rounded-2xl shadow-sm border border-slate-100">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Ringkasan statistik data spasial Kota Padang</p>
            </div>
        </div>
    </x-slot>

    @push('styles')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            .glass-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(16px);
                border: 1px solid rgba(255,255,255,0.4);
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
            }
            .stat-value {
                background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    @endpush

    <div class="space-y-6">
        
        <!-- SUMMARY CARDS (Compact) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card 1 -->
            <div class="glass-card rounded-2xl p-5 flex items-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="p-3 bg-indigo-50/80 rounded-xl mr-4 border border-indigo-100/50">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Lokasi</p>
                    <p class="text-2xl font-extrabold stat-value">{{ number_format($totalItems) }}</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="glass-card rounded-2xl p-5 flex items-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="p-3 bg-emerald-50/80 rounded-xl mr-4 border border-emerald-100/50">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kategori Layer</p>
                    <p class="text-2xl font-extrabold stat-value">{{ number_format($totalLayers) }}</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="glass-card rounded-2xl p-5 flex items-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                <div class="p-3 bg-amber-50/80 rounded-xl mr-4 border border-amber-100/50">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kecamatan Aktif</p>
                    <p class="text-2xl font-extrabold stat-value">{{ number_format($totalKecamatan) }}</p>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION (Compact) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            
            <!-- TREND CHART -->
            <div class="glass-card rounded-2xl p-5 lg:col-span-2 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Tren Penambahan Data</h3>
                        <p class="text-xs text-slate-500">Pertumbuhan lokasi baru</p>
                    </div>
                </div>
                
                <div class="relative flex-1 w-full h-64">
                    @if($trendData->isEmpty())
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-600 mb-1">Belum Ada Data</h4>
                            <p class="text-xs text-slate-400 max-w-[200px] mx-auto">Mulai petakan lokasi untuk melihat grafik.</p>
                        </div>
                    @else
                        <canvas id="trendChart"></canvas>
                    @endif
                </div>
            </div>

            <!-- DOUGHNUT CHART -->
            <div class="glass-card rounded-2xl p-5 flex flex-col">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-800">Sebaran Kategori</h3>
                    <p class="text-xs text-slate-500">Komposisi berdasarkan layer</p>
                </div>
                
                <div class="relative flex-1 w-full h-64 flex items-center justify-center">
                    @if($layerData->isEmpty())
                        <div class="text-center">
                            <p class="text-xs text-slate-400">Data belum tersedia.</p>
                        </div>
                    @else
                        <canvas id="categoryChart"></canvas>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            Chart.defaults.font.family = "'Poppins', sans-serif";
            
            const trendData = @json($trendData);
            const layerData = @json($layerData);
            
            // 1. TREN CHART
            if(trendData.length > 0) {
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                let gradient = ctxTrend.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
                
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.date),
                        datasets: [{
                            label: 'Lokasi Baru',
                            data: trendData.map(d => d.count),
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { size: 12 },
                                bodyFont: { size: 13, weight: 'bold' },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#f1f5f9' },
                                ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } }
                            },
                            x: {
                                border: { display: false },
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // 2. CATEGORY CHART
            if(layerData.length > 0) {
                const ctxCat = document.getElementById('categoryChart').getContext('2d');
                
                new Chart(ctxCat, {
                    type: 'doughnut',
                    data: {
                        labels: layerData.map(d => d.nama),
                        datasets: [{
                            data: layerData.map(d => d.items_count),
                            backgroundColor: layerData.map(d => d.warna),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#64748b',
                                    font: { size: 11 }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 10,
                                bodyFont: { size: 12 },
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }

        });
    </script>
    @endpush
</x-admin-layout>
