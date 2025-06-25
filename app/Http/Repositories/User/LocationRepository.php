<?php

namespace App\Http\Repositories\User;

use Illuminate\Support\Facades\DB;

class LocationRepository {
    public static function getListLocations() {
        $query = DB::table('locations')
        ->select(
            'id',
            'name',
        )
        ->get();

        return $query;
    }

    public static function getLocationDetail($location_id) {
        $query = DB::table('locations')
        ->where('id', $location_id)
        ->select(
            'id',
            'name',
        )
        ->first();

        return $query;
    }
}