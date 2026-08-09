@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="loadData()">
    <div x-show="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data...</p>
        </div>
    </div>

    <div x-show="!loading" class="space-y-6">
        <!-- Stats Cards dengan Warna Solid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-blue-200 text-sm mb-1">Total Users</p>
                <p class="text-3xl font-bold" x-text="stats.total_users"></p>
            </div>

            <div class="bg-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-green-200 text-sm mb-1">Buyers</p>
                <p class="text-3xl font-bold" x-text="stats.total_buyers"></p>
            </div>

            <div class="bg-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-purple-200 text-sm mb-1">Merchants</p>
                <p class="text-3xl font-bold" x-text="stats.total_merchants"></p>
            </div>

            <div class="bg-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-orange-200 text-sm mb-1">Locations</p>
                <p class="text-3xl font-bold" x-text="stats.total_locations"></p>
            </div>
        </div>

        <!-- Quick Actions & Admin Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-700 px-6 py-4">
                    <h3 class="font-bold text-white text-lg">Quick Actions</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.users.index') }}" class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="w-11 h-11 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold text-gray-800">Lihat Semua User</span>
                    </a>
                    
                    <a href="{{ route('admin.users.create') }}" class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-green-400 hover:bg-green-50 transition">
                        <div class="w-11 h-11 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold text-gray-800">Tambah User Baru</span>
                    </a>
                    
                    <a href="{{ route('admin.locations.index') }}" class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-orange-400 hover:bg-orange-50 transition">
                        <div class="w-11 h-11 bg-orange-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold text-gray-800">Lihat Semua Lokasi</span>
                    </a>
                    
                    <a href="{{ route('admin.locations.create') }}" class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-purple-400 hover:bg-purple-50 transition">
                        <div class="w-11 h-11 bg-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold text-gray-800">Tambah Lokasi Baru</span>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Admin Info</h3>
                <div class="space-y-5">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Admin</p>
                        <p class="font-bold text-gray-900" x-text="admin.name"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="font-semibold text-gray-800 break-all" x-text="admin.email"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Role</p>
                        <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" x-text="admin.role_name"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function dashboardData() {
        return {
            loading: true,
            stats: {
                total_users: 0,
                total_buyers: 0,
                total_merchants: 0,
                total_admins: 0,
                total_locations: 0
            },
            admin: {
                name: '',
                email: '',
                phone_number: '',
                role_name: ''
            },
            
            async loadData() {
                if (!checkAuth()) return;
                
                try {
                    const data = await apiCall('/admin/dashboard');
                    
                    if (data.code === 200) {
                        this.stats = {
                            total_users: data.data.total_users || 0,
                            total_buyers: data.data.total_buyers || 0,
                            total_merchants: data.data.total_merchants || 0,
                            total_admins: data.data.total_admins || 0,
                            total_locations: data.data.total_locations || 0
                        };
                        
                        this.admin = data.data.admin || {};
                    } else {
                        console.error('Failed to load dashboard data:', data);
                    }
                } catch (error) {
                    console.error('Error loading dashboard:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endpush
