<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Parabite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-800 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-blue-700 px-8 py-10 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-5">
                    <svg class="w-12 h-12 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Parabite Admin</h2>
                <p class="text-blue-200 text-sm mt-2">Silakan login untuk mengakses dashboard</p>
            </div>
            
            <div class="px-8 py-8">
                <form class="space-y-5" x-data="loginForm()" @submit.prevent="handleSubmit">
                    <div x-show="errorMessage" class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg flex items-center" x-cloak>
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="errorMessage"></span>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            x-model="formData.email"
                            required 
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="admin@parabite.com">
                        <p x-show="errors.email" class="mt-1.5 text-sm text-red-600" x-text="errors.email" x-cloak></p>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password"
                            x-model="formData.password" 
                            required 
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="••••••••">
                        <p x-show="errors.password" class="mt-1.5 text-sm text-red-600" x-text="errors.password" x-cloak></p>
                    </div>
                    
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <span x-show="!loading" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login
                            </span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="text-center text-sm text-blue-200 mt-6">
            &copy; 2026 Parabite. All rights reserved.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        const API_BASE_URL = '/api/v1';
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                formData: {
                    email: '',
                    password: ''
                },
                errors: {},
                errorMessage: '',
                loading: false,
                
                async handleSubmit() {
                    this.errors = {};
                    this.errorMessage = '';
                    this.loading = true;
                    
                    try {
                        const response = await fetch(API_BASE_URL + '/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });
                        
                        const data = await response.json();
                        
                        if (data.code === 200) {
                            if (data.data.account.role_name !== 'admin') {
                                this.errorMessage = 'Akses ditolak. Anda bukan admin.';
                                return;
                            }
                            
                            localStorage.setItem('admin_token', data.data.token);
                            localStorage.setItem('admin_data', JSON.stringify(data.data.account));
                            
                            window.location.href = '{{ route("admin.dashboard") }}';
                        } else if (data.code === 422) {
                            this.errors = data.errors || {};
                            this.errorMessage = 'Validasi gagal. Periksa input Anda.';
                        } else if (data.code === 401) {
                            this.errorMessage = 'Email atau password salah.';
                        } else if (data.code === 403) {
                            this.errorMessage = 'Akun Anda tidak aktif.';
                        } else {
                            this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                    } catch (error) {
                        console.error('Login error:', error);
                        this.errorMessage = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
