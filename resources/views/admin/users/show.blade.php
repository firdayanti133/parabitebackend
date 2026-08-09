@extends('layouts.admin')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div x-data="userDetail()" x-init="loadUser()">
    <div x-show="loading" class="flex justify-center py-20">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data...</p>
        </div>
    </div>

    <div x-show="!loading" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center mb-8 pb-8 border-b border-gray-100">
                            <div class="w-24 h-24 rounded-2xl bg-indigo-500 flex items-center justify-center text-white font-bold text-4xl shadow-xl" x-text="user.name ? user.name.charAt(0).toUpperCase() : ''"></div>
                            <div class="ml-8">
                                <h3 class="text-3xl font-bold text-gray-900 mb-1" x-text="user.name"></h3>
                                <p class="text-gray-500 font-medium" x-text="'User ID: #' + user.id"></p>
                                <div class="mt-3 flex gap-2">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full"
                                          :class="{
                                              'bg-red-100 text-red-800 ring-1 ring-red-500': user.role_name === 'admin',
                                              'bg-indigo-100 text-indigo-800 ring-1 ring-indigo-500': user.role_name === 'merchant',
                                              'bg-blue-100 text-blue-800 ring-1 ring-blue-500': user.role_name === 'user'
                                          }" x-text="user.role_name"></span>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full"
                                          :class="user.is_active ? 'bg-green-100 text-green-800 ring-1 ring-green-500' : 'bg-gray-100 text-gray-800 ring-1 ring-gray-500'"
                                          x-text="user.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            Informasi Kontak & Akun
                        </h4>

                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email Address</dt>
                                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span x-text="user.email"></span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Phone Number</dt>
                                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 004.816 4.816l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span x-text="user.phone_number || '-'"></span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Bergabung</dt>
                                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span x-text="formatDate(user.created_at)"></span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <dt class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Update Terakhir</dt>
                                <dd class="text-sm font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="formatDate(user.updated_at)"></span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-indigo-900 rounded-2xl shadow-xl p-8 text-white">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2zm7 0v-3a2 2 0 00-2-2h-3a2 2 0 00-2 2v3M4 11V4a2 2 0 012-2h10a2 2 0 012 2v7"></path>
                            </svg>
                        </div>
                        Statistik User
                    </h3>
                    
                    <div x-show="!related" class="flex justify-center py-4">
                        <div class="animate-pulse flex space-x-2">
                            <div class="h-2 w-2 bg-white/50 rounded-full"></div>
                            <div class="h-2 w-2 bg-white/50 rounded-full"></div>
                            <div class="h-2 w-2 bg-white/50 rounded-full"></div>
                        </div>
                    </div>

                    <div x-show="related" class="space-y-4">
                        <template x-for="(value, key) in related" :key="key">
                            <div class="bg-white/10 rounded-xl p-4 flex justify-between items-center backdrop-blur-sm hover:bg-white/20 transition duration-200">
                                <span class="text-sm font-semibold capitalize" x-text="key.replace('_', ' ')"></span>
                                <span class="text-xl font-bold bg-white text-indigo-600 px-3 py-1 rounded-lg min-w-[3rem] text-center shadow-lg" x-text="value"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m10 0a2 2 0 100-4m0 4a2 2 0 110-4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m-6-2a2 2 0 100-4m0 4a2 2 0 110-4m-6 0a2 2 0 100-4m0 4a2 2 0 110-4m0-4v2m0-6V4"></path>
                            </svg>
                        </div>
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <a :href="`{{ route('admin.users.index') }}/${userId}/edit`" class="flex items-center justify-center w-full px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            Edit Profil User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center w-full px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function userDetail() {
        return {
            loading: true,
            userId: '{{ $id ?? 0 }}',
            user: {},
            related: null,
            
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            },
            
            async loadUser() {
                if (!checkAuth()) return;
                try {
                    const data = await apiCall(`/admin/users/${this.userId}`);
                    if (data.code === 200) {
                        this.user = data.data;
                        this.related = data.data.related_records;
                    }
                } catch (error) {
                    console.error('Error loading user:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endpush

