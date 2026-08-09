@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')
@section('page-title', 'Manajemen Lokasi')

@section('content')
<div x-data="locationsData()" x-init="loadLocations()">
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-indigo-600 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    <div x-show="errorMessage" class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl shadow-sm flex items-center" x-cloak>
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span x-text="errorMessage"></span>
    </div>
    
    <div class="mb-6 space-y-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        x-model="search" 
                        @input.debounce.500ms="loadLocations(1)"
                        placeholder="Cari nama lokasi..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                </div>
            </div>
            
            <div>
                <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center justify-center w-full lg:w-auto px-5 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl font-medium whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah Lokasi</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden">
        <div x-show="loading" class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data...</p>
        </div>

        <div x-show="!loading">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-indigo-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dibuat Pada</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="location in locations" :key="location.id">
                            <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600" x-text="'#' + location.id"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900" x-text="location.name"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600" x-text="formatDate(location.created_at)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a :href="`{{ route('admin.locations.index') }}/${location.id}/edit`" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</a>
                                    <button @click="confirmDelete(location)" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="locations.length === 0" class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                </svg>
                <p class="text-gray-600 font-medium">Tidak ada data lokasi</p>
            </div>

            <div x-show="pagination.total_page > 1" class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700 font-medium">
                        Menampilkan <span class="font-bold text-indigo-600" x-text="locations.length"></span> dari <span class="font-bold text-indigo-600" x-text="pagination.total_data"></span> lokasi
                    </div>
                    <div class="flex gap-2">
                        <button 
                            @click="loadLocations(pagination.page - 1)"
                            :disabled="pagination.page <= 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-300 transition">
                            Prev
                        </button>
                        <button 
                            @click="loadLocations(pagination.page + 1)"
                            :disabled="pagination.page >= pagination.total_page"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-300 transition">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="deleteModal.show" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" @click.away="deleteModal.show = false">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Konfirmasi Hapus Lokasi</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus lokasi "<span class="font-semibold text-gray-900" x-text="deleteModal.location?.name"></span>"?</p>
                <div class="flex gap-3">
                    <button @click="deleteModal.show = false" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="deleteLocation" :disabled="deleting" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="deleting ? 'Memproses...' : 'Hapus'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function locationsData() {
        return {
            loading: true,
            locations: [],
            search: '',
            errorMessage: '',
            deleting: false,
            pagination: {
                page: 1,
                limit: 10,
                total_data: 0,
                total_page: 0
            },
            deleteModal: {
                show: false,
                location: null
            },
            
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },
            
            async loadLocations(page = 1) {
                if (!checkAuth()) return;
                this.loading = true;
                this.errorMessage = '';
                try {
                    const params = new URLSearchParams({
                        page: page,
                        limit: this.pagination.limit
                    });
                    
                    if (this.search) {
                        params.append('search', this.search);
                    }
                    
                    const data = await apiCall(`/admin/locations?${params}`);
                    if (data.code === 200) {
                        this.locations = data.data.data || [];
                        this.pagination = {
                            page: data.data.page,
                            limit: data.data.limit,
                            total_data: data.data.total_data,
                            total_page: data.data.total_page
                        };
                    } else {
                        this.errorMessage = 'Gagal memuat data: ' + (data.message || 'Error tidak diketahui');
                    }
                } catch (error) {
                    console.error('Error loading locations:', error);
                    this.errorMessage = 'Terjadi kesalahan saat memuat data lokasi. Silakan cek koneksi atau login kembali.';
                } finally {
                    this.loading = false;
                }
            },
            
            confirmDelete(location) {
                this.deleteModal.location = location;
                this.deleteModal.show = true;
            },
            
            async deleteLocation() {
                if (!this.deleteModal.location || this.deleting) return;

                this.deleting = true;
                try {
                    const data = await apiCall(`/admin/locations/${this.deleteModal.location.id}`, {
                        method: 'DELETE'
                    });
                    if (data.code === 200) {
                        showAdminNotification('Lokasi berhasil dihapus.');
                        this.deleteModal.show = false;
                        this.loadLocations(this.pagination.page);
                    } else if (data.code === 409) {
                        showAdminNotification(data.message || 'Lokasi tidak bisa dihapus karena sedang digunakan.', 'error');
                        this.deleteModal.show = false;
                    } else {
                        showAdminNotification(data.message || 'Gagal menghapus lokasi.', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting location:', error);
                    showAdminNotification('Terjadi kesalahan saat menghapus lokasi.', 'error');
                } finally {
                    this.deleting = false;
                }
            }
        }
    }
</script>
@endpush
