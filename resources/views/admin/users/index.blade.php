@extends('layouts.admin')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div x-data="usersData()" x-init="loadUsers()">
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
                        @input.debounce.500ms="loadUsers(1)"
                        placeholder="Cari nama atau email..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm transition">
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <select x-model="roleFilter" @change="loadUsers(1)" class="flex-1 sm:flex-none sm:w-40 px-4 py-3 bg-white border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm transition">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="merchant">Merchant</option>
                    <option value="user">User</option>
                </select>
                
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl font-medium whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah User</span>
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-bold text-lg shadow-lg" x-text="user.name.charAt(0).toUpperCase()"></div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900" x-text="user.name"></div>
                                            <div class="text-xs text-gray-500" x-text="'ID: ' + user.id"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700" x-text="user.email"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700" x-text="user.phone_number"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="{
                                              'bg-red-100 text-red-800 ring-1 ring-red-500': user.role_name === 'admin',
                                              'bg-indigo-100 text-indigo-800 ring-1 ring-indigo-500': user.role_name === 'merchant',
                                              'bg-blue-100 text-blue-800 ring-1 ring-blue-500': user.role_name === 'user'
                                          }"
                                          x-text="user.role_name"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="user.is_active ? 'bg-green-100 text-green-800 ring-1 ring-green-500' : 'bg-gray-100 text-gray-800 ring-1 ring-gray-500'"
                                          x-text="user.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a :href="`{{ route('admin.users.index') }}/${user.id}`" class="text-blue-600 hover:text-blue-900 mr-4 font-semibold">Detail</a>
                                    <a :href="`{{ route('admin.users.index') }}/${user.id}/edit`" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</a>
                                    <button @click="confirmDelete(user)" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="users.length === 0" class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-600 font-medium">Tidak ada data user</p>
            </div>

            <div x-show="pagination.total_page > 1" class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700 font-medium">
                        Menampilkan <span class="font-bold text-indigo-600" x-text="users.length"></span> dari <span class="font-bold text-indigo-600" x-text="pagination.total_data"></span> user
                    </div>
                    <div class="flex gap-2">
                        <button 
                            @click="loadUsers(pagination.page - 1)"
                            :disabled="pagination.page <= 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <template x-for="page in paginationPages" :key="page">
                            <button 
                                @click="loadUsers(page)"
                                :class="page === pagination.page ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-indigo-50 border border-gray-300'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                x-text="page"></button>
                        </template>
                        
                        <button 
                            @click="loadUsers(pagination.page + 1)"
                            :disabled="pagination.page >= pagination.total_page"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
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
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Konfirmasi Hapus User</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menonaktifkan user "<span class="font-semibold text-gray-900" x-text="deleteModal.user?.name"></span>"?</p>
                <div class="flex gap-3">
                    <button @click="deleteModal.show = false" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button @click="deleteUser" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition shadow-lg">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function usersData() {
        return {
            loading: true,
            users: [],
            search: '',
            roleFilter: '',
            errorMessage: '',
            pagination: {
                page: 1,
                limit: 10,
                total_data: 0,
                total_page: 0
            },
            deleteModal: {
                show: false,
                user: null
            },
            
            get paginationPages() {
                const pages = [];
                const total = this.pagination.total_page;
                const current = this.pagination.page;
                
                if (total <= 7) {
                    for (let i = 1; i <= total; i++) pages.push(i);
                } else {
                    if (current <= 4) {
                        for (let i = 1; i <= 5; i++) pages.push(i);
                        pages.push('...');
                        pages.push(total);
                    } else if (current >= total - 3) {
                        pages.push(1);
                        pages.push('...');
                        for (let i = total - 4; i <= total; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        pages.push('...');
                        for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                        pages.push('...');
                        pages.push(total);
                    }
                }
                
                return pages;
            },
            
            async loadUsers(page = 1) {
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
                    
                    if (this.roleFilter) {
                        params.append('role', this.roleFilter);
                    }
                    
                    const data = await apiCall(`/admin/users?${params}`);
                    
                    if (data.code === 200) {
                        this.users = data.data.data || [];
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
                    console.error('Error loading users:', error);
                    this.errorMessage = 'Terjadi kesalahan saat memuat data user. Silakan cek koneksi atau login kembali.';
                } finally {
                    this.loading = false;
                }
            },
            
            confirmDelete(user) {
                this.deleteModal.user = user;
                this.deleteModal.show = true;
            },
            
            async deleteUser() {
                if (!this.deleteModal.user) return;
                
                try {
                    const data = await apiCall(`/admin/users/${this.deleteModal.user.id}`, {
                        method: 'DELETE'
                    });
                    
                    if (data.code === 200) {
                        alert('User berhasil dinonaktifkan');
                        this.deleteModal.show = false;
                        this.loadUsers(this.pagination.page);
                    } else {
                        alert(data.message || 'Gagal menghapus user');
                    }
                } catch (error) {
                    console.error('Error deleting user:', error);
                    alert('Terjadi kesalahan saat menghapus user');
                }
            }
        }
    }
</script>
@endpush

