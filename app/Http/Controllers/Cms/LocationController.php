<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

use Illuminate\Support\Facades\Validator;
use Exception;

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

    public function createLocation(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $location = Location::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $location
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error',
                'status' => 500,
                'data' => null
            ], 500);
        }
    }
}
