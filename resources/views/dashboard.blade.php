<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight">
            {{ __('Dashboard Statistik') }}
        </h2>
    </x-slot>

    @push('styles')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            }
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .hover-lift:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
        </style>
    @endpush

    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="glass-card rounded-2xl p-6 flex items-center hover-lift relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-50 pointer-events-none"></div>
                    <div class="p-4 bg-indigo-100 rounded-xl mr-5">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Titik & Area</p>
                        <p class="text-3xl font-black text-slate-800 mt-1">{{ number_format($totalItems) }}</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="glass-card rounded-2xl p-6 flex items-center hover-lift relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 pointer-events-none"></div>
                    <div class="p-4 bg-emerald-100 rounded-xl mr-5">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Kategori Layer</p>
                        <p class="text-3xl font-black text-slate-800 mt-1">{{ number_format($totalLayers) }}</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="glass-card rounded-2xl p-6 flex items-center hover-lift relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full opacity-50 pointer-events-none"></div>
                    <div class="p-4 bg-amber-100 rounded-xl mr-5">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Kecamatan Aktif</p>
                        <p class="text-3xl font-black text-slate-800 mt-1">{{ number_format($totalKecamatan) }}</p>
                    </div>
                </div>
            </div>

            <!-- CHARTS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- TREND CHART -->
                <div class="glass-card rounded-2xl p-6 lg:col-span-2 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Tren Penambahan Data</h3>
                            <p class="text-sm text-slate-500">Statistik lokasi baru berdasarkan tanggal input</p>
                        </div>
                        <a href="{{ route('peta.index') }}" class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-lg hover:bg-indigo-100 transition">Lihat Peta &rarr;</a>
                    </div>
                    
                    <div class="relative flex-1 w-full min-h-[300px]">
                        @if($trendData->isEmpty())
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                </div>
                                <h4 class="text-slate-600 font-bold mb-1">Belum Ada Data</h4>
                                <p class="text-sm text-slate-400 max-w-xs mx-auto">Silakan mulai melakukan pemetaan lokasi pada menu Peta untuk melihat statistik tren di sini.</p>
                            </div>
                        @else
                            <canvas id="trendChart"></canvas>
                        @endif
                    </div>
                </div>

                <!-- DOUGHNUT CHART -->
                <div class="glass-card rounded-2xl p-6 flex flex-col">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800">Sebaran per Kategori</h3>
                        <p class="text-sm text-slate-500">Komposisi data lokasi berdasarkan layer</p>
                    </div>
                    
                    <div class="relative flex-1 w-full min-h-[300px] flex items-center justify-center">
                        @if($layerData->isEmpty())
                            <div class="text-center">
                                <p class="text-sm text-slate-400">Data belum tersedia.</p>
                            </div>
                        @else
                            <canvas id="categoryChart"></canvas>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- DATA SETUP ---
            const trendData = @json($trendData);
            const layerData = @json($layerData);
            
            // 1. TREN CHART (LINE)
            if(trendData.length > 0) {
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                
                // Gradient Fill
                let gradient = ctxTrend.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // indigo-600
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
                
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.date),
                        datasets: [{
                            label: 'Jumlah Lokasi Baru',
                            data: trendData.map(d => d.count),
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4 // Smooth curves
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { size: 13, family: 'Inter' },
                                bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9', drawBorder: false },
                                ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'Inter' } }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#94a3b8', font: { family: 'Inter' } }
                            }
                        }
                    }
                });
            }

            // 2. CATEGORY CHART (DOUGHNUT)
            if(layerData.length > 0) {
                const ctxCat = document.getElementById('categoryChart').getContext('2d');
                
                new Chart(ctxCat, {
                    type: 'doughnut',
                    data: {
                        labels: layerData.map(d => d.nama),
                        datasets: [{
                            data: layerData.map(d => d.items_count),
                            backgroundColor: layerData.map(d => d.warna),
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#64748b',
                                    font: { family: 'Inter', size: 12 }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 12,
                                bodyFont: { family: 'Inter', size: 13 },
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
