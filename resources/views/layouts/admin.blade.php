<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Parabite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hover-lift { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -10px rgba(59, 130, 246, 0.4); }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col lg:flex-row" x-data="adminLayout()">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-72 bg-blue-900 text-white transform lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 shadow-2xl" 
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex items-center justify-between h-20 px-6 bg-blue-950 border-b border-blue-800">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-blue-900" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight">Parabite</h1>
                        <p class="text-xs text-blue-300">Admin Panel</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg bg-blue-800 hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <nav class="mt-8 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-blue-900' : 'text-blue-100 hover:bg-blue-800' }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-blue-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-semibold">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-white text-blue-900' : 'text-blue-100 hover:bg-blue-800' }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.users.*') ? 'bg-green-100 text-green-700' : 'text-blue-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-semibold">Manajemen User</span>
                </a>
                
                <a href="{{ route('admin.locations.index') }}" 
                   class="flex items-center px-4 py-3 rounded-xl transition-colors duration-200 {{ request()->routeIs('admin.locations.*') ? 'bg-white text-blue-900' : 'text-blue-100 hover:bg-blue-800' }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.locations.*') ? 'bg-orange-100 text-orange-700' : 'text-blue-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-semibold">Manajemen Lokasi</span>
                </a>
            </nav>
            
            <div class="p-5 bg-blue-950 border-t border-blue-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-blue-900 font-bold shadow-lg flex-shrink-0">
                            <span class="text-lg" x-text="adminInitial"></span>
                        </div>
                        <div class="ml-3 truncate">
                            <p class="text-sm font-bold text-white truncate" x-text="adminName"></p>
                            <p class="text-xs text-blue-300">Administrator</p>
                        </div>
                    </div>
                    <button @click="logout" class="ml-2 p-2.5 rounded-lg bg-blue-800 hover:bg-blue-700 text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-72">
            <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
                <div class="h-20 px-6 lg:px-8 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-3 text-sm text-gray-600 bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-200">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline font-medium" x-text="currentDate"></span>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Overlay untuk Mobile Sidebar -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-20 lg:hidden"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        const API_BASE_URL = '/api/v1';
        
        function getToken() {
            return localStorage.getItem('admin_token');
        }
        
        function setToken(token) {
            localStorage.setItem('admin_token', token);
        }
        
        function clearToken() {
            localStorage.removeItem('admin_token');
        }
        
        function getAdminData() {
            const data = localStorage.getItem('admin_data');
            return data ? JSON.parse(data) : null;
        }
        
        function setAdminData(data) {
            localStorage.setItem('admin_data', JSON.stringify(data));
        }
        
        function clearAdminData() {
            localStorage.removeItem('admin_data');
        }
        
        async function apiCall(url, options = {}) {
            const token = getToken();
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            try {
                const response = await fetch(API_BASE_URL + url, {
                    ...options,
                    headers
                });
                
                const data = await response.json();
                
                if (response.status === 401) {
                    clearToken();
                    clearAdminData();
                    window.location.href = '{{ route("admin.login") }}';
                    throw new Error('Unauthorized');
                }
                
                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }
        
        function checkAuth() {
            const token = getToken();
            if (!token) {
                window.location.href = '{{ route("admin.login") }}';
                return false;
            }
            return true;
        }
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminLayout', () => ({
                sidebarOpen: false,
                adminName: '',
                adminInitial: '',
                currentDate: '',
                
                init() {
                    const adminData = getAdminData();
                    if (adminData) {
                        this.adminName = adminData.name || 'Admin';
                        this.adminInitial = this.adminName.charAt(0).toUpperCase();
                    }
                    
                    this.updateDate();
                    setInterval(() => this.updateDate(), 60000);
                },
                
                updateDate() {
                    const now = new Date();
                    const options = { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    };
                    this.currentDate = now.toLocaleDateString('id-ID', options);
                },
                
                async logout() {
                    if (!confirm('Apakah Anda yakin ingin keluar?')) return;
                    
                    try {
                        await apiCall('/logout', { method: 'POST' });
                    } catch (error) {
                        console.error('Logout error:', error);
                    } finally {
                        clearToken();
                        clearAdminData();
                        window.location.href = '{{ route("admin.login") }}';
                    }
                }
            }));
        });
    </script>
    
    @stack('scripts')
</body>
</html>
