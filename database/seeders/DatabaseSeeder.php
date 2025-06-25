<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\LocationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@user.com',
            'phone_number' => '08123456789',
            'password' => Hash::make('adminuser13'),
            'role_name' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Admin Merchant',
            'email' => 'admin@merchant.com',
            'phone_number' => '08123456789',
            'password' => Hash::make('adminmerchant13'),
            'role_name' => 'merchant',
            'is_merchant' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call(LocationSeeder::class);
    }
}
