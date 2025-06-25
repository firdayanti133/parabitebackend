<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class UserRepository {
    public static function createUser($data) {
        DB::table('users')
        ->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone_number' => $data['phone_number'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public static function getUserExpense($user_id) {
        $query = DB::table('user_orders')
        ->where('user_id', $user_id)
        ->sum('bill');

        return $query;
    }

    public static function getUserTotalOrder($user_id) {
        $query = DB::table('user_orders')
        ->where('user_id', $user_id)
        ->count();

        return $query;
    }
}