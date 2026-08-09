@extends('layouts.admin')

@section('title', isset($id) ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('page-title', isset($id) ? 'Edit Lokasi' : 'Tambah Lokasi')

@section('content')
<div x-data="locationForm()" x-init="initForm()">
    <div x-show="loading" class="flex justify-center py-20">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data...</p>
        </div>
    </div>

    <div x-show="!loading" class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-8">
            <div class="flex items-center mb-8 pb-6 border-b border-gray-100">
                <div class="w-14 h-14 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/50 mr-5">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ isset($id) ? 'Edit Lokasi' : 'Tambah Lokasi Baru' }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Masukkan informasi lokasi di bawah ini</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div x-show="errorMessage" class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl shadow-sm flex items-center" x-cloak>
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="errorMessage"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lokasi</label>
                    <input type="text" x-model="formData.name" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" placeholder="Contoh: Gedung A, Lantai 1">
                    <p class="mt-2 text-xs text-gray-500">Nama lokasi yang akan ditampilkan kepada pengguna</p>
                    <p x-show="errors.name" class="mt-2 text-sm text-red-600" x-text="errors.name" x-cloak></p>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.locations.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-center min-w-[120px]">
                        Kembali
                    </a>
                    <button type="submit" :disabled="submitting" class="px-8 py-3 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105">
                        <span x-show="!submitting" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Lokasi
                        </span>
                        <span x-show="submitting" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function locationForm() {
        return {
            loading: {{ isset($id) ? 'true' : 'false' }},
            submitting: false,
            isEdit: {{ isset($id) ? 'true' : 'false' }},
            locationId: '{{ $id ?? "" }}',
            formData: {
                name: ''
            },
            errors: {},
            errorMessage: '',
            
            async initForm() {
                if (this.isEdit) {
                    await this.loadLocationData();
                }
            },
            
            async loadLocationData() {
                try {
                    const data = await apiCall(`/admin/locations/${this.locationId}`);
                    if (data.code === 200) {
                        this.formData.name = data.data.name;
                    } else {
                        this.errorMessage = data.message || 'Gagal memuat data lokasi.';
                    }
                } catch (error) {
                    console.error('Error loading location:', error);
                    this.errorMessage = 'Gagal memuat data lokasi.';
                } finally {
                    this.loading = false;
                }
            },
            
            async handleSubmit() {
                this.errors = {};
                this.errorMessage = '';
                this.submitting = true;
                
                try {
                    const url = this.isEdit ? `/admin/locations/${this.locationId}` : '/admin/locations';
                    const method = this.isEdit ? 'PUT' : 'POST';
                    
                    const data = await apiCall(url, {
                        method: method,
                        body: JSON.stringify(this.formData)
                    });
                    
                    if (data.code === 200 || data.code === 201) {
                        queueAdminNotification(this.isEdit ? 'Lokasi berhasil diperbarui.' : 'Lokasi berhasil ditambahkan.');
                        window.location.href = '{{ route("admin.locations.index") }}';
                    } else if (data.code === 422) {
                        this.errors = data.errors || {};
                        this.errorMessage = 'Validasi gagal.';
                    } else {
                        this.errorMessage = data.message || 'Terjadi kesalahan.';
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    this.errorMessage = 'Terjadi kesalahan sistem.';
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
@endpush
