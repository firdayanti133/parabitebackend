@extends('layouts.admin')

@section('title', isset($id) ? 'Edit User' : 'Tambah User')
@section('page-title', isset($id) ? 'Edit User' : 'Tambah User')

@section('content')
<div x-data="userForm()" x-init="initForm()">
    <div x-show="loading" class="flex justify-center py-20">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data...</p>
        </div>
    </div>

    <div x-show="!loading" class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-8">
            <form @submit.prevent="handleSubmit" class="space-y-8">
                <div x-show="errorMessage" class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl shadow-sm flex items-center" x-cloak>
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="errorMessage"></span>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Informasi Personal
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="formData.name" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p x-show="errors.name" class="mt-2 text-sm text-red-600" x-text="errors.name" x-cloak></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="formData.email" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p x-show="errors.email" class="mt-2 text-sm text-red-600" x-text="errors.email" x-cloak></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                            <input type="tel" x-model="formData.phone_number" required inputmode="tel" autocomplete="tel" minlength="8" maxlength="16" pattern="\+?[0-9]{8,15}" placeholder="081234567890 atau +6281234567890" class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p x-show="errors.phone_number" class="mt-2 text-sm text-red-600" x-text="errors.phone_number" x-cloak></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select x-model="formData.role_name" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="user">User</option>
                                <option value="merchant">Merchant</option>
                                <option value="admin">Admin</option>
                            </select>
                            <p x-show="errors.role_name" class="mt-2 text-sm text-red-600" x-text="errors.role_name" x-cloak></p>
                        </div>
                    </div>
                </div>

                @if(isset($id))
                <div class="bg-indigo-50 rounded-xl p-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.is_active" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition">
                        <span class="ml-3 text-sm font-semibold text-gray-700">Akun Aktif</span>
                    </label>
                    <p class="mt-2 text-xs text-gray-600 ml-8">Nonaktifkan untuk memblokir akses user ke sistem</p>
                </div>
                @endif

                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        {{ isset($id) ? 'Ubah Password (opsional)' : 'Set Password' }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" x-model="formData.password" :required="!isEdit" class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p class="mt-2 text-xs text-gray-500">Minimal 8 karakter</p>
                            <p x-show="errors.password" class="mt-2 text-sm text-red-600" x-text="errors.password" x-cloak></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                            <input type="password" x-model="formData.confirmed_password" :required="formData.password" class="block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p class="mt-2 text-xs text-gray-500">Ketik ulang password</p>
                            <p x-show="errors.confirmed_password" class="mt-2 text-sm text-red-600" x-text="errors.confirmed_password" x-cloak></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-center min-w-[120px]">
                        Kembali
                    </a>
                    <button type="submit" :disabled="submitting" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:scale-105">
                        <span x-show="!submitting" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Data
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
    function userForm() {
        return {
            loading: {{ isset($id) ? 'true' : 'false' }},
            submitting: false,
            isEdit: {{ isset($id) ? 'true' : 'false' }},
            userId: '{{ $id ?? "" }}',
            formData: {
                name: '',
                email: '',
                phone_number: '',
                role_name: 'user',
                is_active: true,
                password: '',
                confirmed_password: ''
            },
            errors: {},
            errorMessage: '',
            
            async initForm() {
                if (this.isEdit) {
                    await this.loadUserData();
                }
            },
            
            async loadUserData() {
                try {
                    const data = await apiCall(`/admin/users/${this.userId}`);
                    if (data.code === 200) {
                        this.formData = {
                            ...this.formData,
                            name: data.data.name,
                            email: data.data.email,
                            phone_number: data.data.phone_number,
                            role_name: data.data.role_name,
                            is_active: data.data.is_active,
                            password: '',
                            confirmed_password: ''
                        };
                    } else {
                        this.errorMessage = data.message || 'Gagal memuat data user.';
                    }
                } catch (error) {
                    console.error('Error loading user:', error);
                    this.errorMessage = 'Gagal memuat data user.';
                } finally {
                    this.loading = false;
                }
            },
            
            async handleSubmit() {
                this.errors = {};
                this.errorMessage = '';
                this.submitting = true;
                
                try {
                    const url = this.isEdit ? `/admin/users/${this.userId}` : '/admin/users';
                    const method = this.isEdit ? 'PUT' : 'POST';
                    
                    const payload = { ...this.formData };
                    if (this.isEdit && !payload.password) {
                        delete payload.password;
                        delete payload.confirmed_password;
                    }
                    
                    const data = await apiCall(url, {
                        method: method,
                        body: JSON.stringify(payload)
                    });
                    
                    if (data.code === 200 || data.code === 201) {
                        queueAdminNotification(this.isEdit ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.');
                        window.location.href = '{{ route("admin.users.index") }}';
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
