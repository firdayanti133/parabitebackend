<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MerchantMenu;
use Exception;

class MenuController extends Controller
{
    public function getAllMenus() {
        try {
            $menus = MerchantMenu::all();

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $menus
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'error',
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function getMenusByMerchant($merchant_id) {
        $validator = Validator::make(['id' => $merchant_id], [
            'id' => 'required|integer|exists:merchants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $menus = MerchantMenu::where('merchant_id', $merchant_id)->get();

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $menus
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
