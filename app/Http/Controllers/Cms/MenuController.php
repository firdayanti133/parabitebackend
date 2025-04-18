<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\MerchantMenu;
use App\Models\Food;

class MenuController extends Controller
{
    public function getAllMenus($merchant_id) {
        $validator = Validator::make(['id' => $merchant_id], [
            'id' => 'required|integer|exists:merchants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
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
                'message' => 'error' . $e,
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function createMenu(Request $request, $id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:merchants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'food_id' => 'required|integer|exists:foods,id',
            'stocks' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'status' => 'required|string|in:available,unavailable,coming soon',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'merchant_id' => $id,
                'food_id' => $request->food_id,
                'stocks' => $request->stocks,
                'price' => $request->price,
                'status' => $request->status,
            ];

            MerchantMenu::create($data);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error',
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function updateMenu(Request $request, $merchant_id, $id) {
        $validator = Validator::make(['id' => $merchant_id], [
            'id' => 'required|integer|exists:merchants,id',
        ]);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:merchant_menu_list,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'food_id' => 'required|integer|exists:foods,id',
            'stocks' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'status' => 'required|string|in:available,unavailable,coming soon',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'food_id' => $request->food_id,
                'stocks' => $request->stocks,
                'price' => $request->price,
                'status' => $request->status,
            ];

            MerchantMenu::where('id', $id)->update($data);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => null  
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error: ' . $e,
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function deleteMenu($merchant_id, $id) {
        $validator = Validator::make(['id' => $merchant_id], [
            'id' => 'required|integer|exists:merchants,id',
        ]);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:merchant_menu_list,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        try {
            MerchantMenu::where('id', $id)->delete();

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => null
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
