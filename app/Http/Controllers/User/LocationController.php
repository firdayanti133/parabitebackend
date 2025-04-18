<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function getLocations() {
        $locations = Location::all();
        return response()->json([
            'message' => 'success',
            'status' => 200,
            'data' => $locations
        ], 200);
    }
}
