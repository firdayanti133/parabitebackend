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
}
