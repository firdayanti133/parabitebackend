<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--list : List existing admin users}';
    protected $description = 'Create a new admin user or list existing admins';

    public function handle()
    {
        if ($this->option('list')) {
            return $this->listAdmins();
        }

        $this->info('=== Create New Admin User ===');
        
        $name = $this->ask('Nama lengkap');
        $email = $this->ask('Email');
        $phone = $this->ask('No. Telepon');
        $password = $this->secret('Password (min 8 karakter)');
        $confirmPassword = $this->secret('Konfirmasi Password');

        if ($password !== $confirmPassword) {
            $this->error('Password tidak cocok!');
            return 1;
        }

        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter!');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Email sudah terdaftar!');
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
            'role_name' => User::ROLE_ADMIN,
            'is_merchant' => false,
            'is_active' => true,
            'password' => Hash::make($password),
        ]);

        $this->info('✓ Admin berhasil dibuat!');
        $this->table(
            ['ID', 'Nama', 'Email', 'Role'],
            [[$user->id, $user->name, $user->email, $user->role_name]]
        );

        return 0;
    }

    private function listAdmins()
    {
        $admins = User::where('role_name', User::ROLE_ADMIN)->get();

        if ($admins->isEmpty()) {
            $this->warn('Tidak ada admin user di database.');
            return 1;
        }

        $this->info('=== Daftar Admin Users ===');
        $this->table(
            ['ID', 'Nama', 'Email', 'No. Telepon', 'Status'],
            $admins->map(fn($admin) => [
                $admin->id,
                $admin->name,
                $admin->email,
                $admin->phone_number,
                $admin->is_active ? 'Aktif' : 'Nonaktif'
            ])->toArray()
        );

        return 0;
    }
}
