<x-admin-layout>
    <div class="max-w-4xl mx-auto py-6" x-data="layerManager()">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Manajemen Kategori / Layer</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola jenis kategori lokasi (seperti penyebaran penyakit, fasilitas, dll).</p>
            </div>
            <button @click="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-sm transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Layer Baru
            </button>
        </div>

        <!-- Layer List -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden">
            
            <template x-if="isLoading">
                <div class="p-12 flex justify-center">
                    <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </template>

            <template x-if="!isLoading && layers.length === 0">
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 border border-slate-100">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada layer yang ditambahkan.</p>
                </div>
            </template>

            <div class="divide-y divide-slate-100" x-show="!isLoading && layers.length > 0">
                <template x-for="layer in layers" :key="layer.id">
                    <div class="p-5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" :style="'background-color: ' + layer.warna + '20'">
                                <span class="w-4 h-4 rounded-full shadow-sm" :style="'background-color: ' + layer.warna"></span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800" x-text="layer.nama"></h3>
                                <p class="text-xs font-semibold text-slate-400 mt-0.5" x-text="layer.deskripsi || 'Tidak ada deskripsi'"></p>
                                <span class="inline-block mt-1.5 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-500 uppercase tracking-wider" x-text="layer.tipe"></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="editLayer(layer)" class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-xl transition-colors" title="Edit Layer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button @click="deleteLayer(layer.id)" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors" title="Hapus Layer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Layer Modal -->
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal()"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="isModalOpen" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-100">
                    <form @submit.prevent="saveLayer">
                        <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-xl leading-6 font-extrabold text-slate-900 mb-6" id="modal-title" x-text="form.id ? 'Edit Layer' : 'Buat Layer Baru'"></h3>
                                    
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Layer *</label>
                                            <input type="text" x-model="form.nama" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5 px-4" placeholder="e.g. Sebaran DBD" required>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                                            <textarea x-model="form.deskripsi" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5 px-4" placeholder="Deskripsi opsional..."></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe Layer *</label>
                                                <select x-model="form.tipe" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all py-2.5 px-4" required>
                                                    <option value="both">Marker & Polygon</option>
                                                    <option value="marker">Hanya Marker</option>
                                                    <option value="polygon">Hanya Polygon</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Warna Penanda *</label>
                                                <div class="flex items-center gap-3">
                                                    <input type="color" x-model="form.warna" class="w-10 h-10 rounded-lg cursor-pointer border-0 bg-transparent p-0">
                                                    <span class="text-sm font-semibold text-slate-600 uppercase" x-text="form.warna"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:ml-3 sm:w-auto sm:text-sm transition-all" :disabled="isSaving">
                                <span x-show="!isSaving">Simpan</span>
                                <span x-show="isSaving">Menyimpan...</span>
                            </button>
                            <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-200 shadow-sm px-6 py-2.5 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-500/10 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('layerManager', () => ({
                layers: [],
                isLoading: true,
                isModalOpen: false,
                isSaving: false,
                form: {
                    id: null,
                    nama: '',
                    deskripsi: '',
                    tipe: 'both',
                    warna: '#4f46e5'
                },

                init() {
                    this.fetchLayers();
                },

                fetchLayers() {
                    this.isLoading = true;
                    fetch('/api/layers')
                        .then(r => r.json())
                        .then(data => {
                            this.layers = data;
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },

                openModal() {
                    this.form = { id: null, nama: '', deskripsi: '', tipe: 'both', warna: '#4f46e5' };
                    this.isModalOpen = true;
                },

                editLayer(layer) {
                    this.form = { ...layer };
                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                },

                saveLayer() {
                    this.isSaving = true;
                    const url = this.form.id ? `/layers/${this.form.id}` : '/layers';
                    const method = this.form.id ? 'PUT' : 'POST';
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    })
                    .then(r => {
                        if(!r.ok) throw new Error('Network response was not ok');
                        return r.json();
                    })
                    .then(() => {
                        this.closeModal();
                        this.fetchLayers();
                    })
                    .catch(err => alert('Gagal menyimpan layer.'))
                    .finally(() => {
                        this.isSaving = false;
                    });
                },

                deleteLayer(id) {
                    if (!confirm('Hapus layer ini beserta seluruh item lokasi di dalamnya?')) return;
                    
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    fetch(`/layers/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => {
                        if(!r.ok) throw new Error('Network response was not ok');
                        this.fetchLayers();
                    })
                    .catch(err => alert('Gagal menghapus layer.'));
                }
            }));
        });
    </script>
    @endpush
</x-admin-layout>
