<?php

namespace App\Http\Repositories\Merchant;

use Illuminate\Support\Facades\DB;

class MerchantRepository {
    public static function createMerchant($data) {
        DB::table('users')
        ->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
            'role_name' => 'merchant',
            'is_merchant' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }
}