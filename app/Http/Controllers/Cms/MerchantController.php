<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\MerchantMenu;
use App\Models\Food;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{
    public function getAllMerchants() {
        $merchants = Merchant::all();

        return response()->json([
            'message' => 'success',
            'status' => 200,
            'data' => $merchants
        ], 200);
    }

    public function createMerchant(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
            'password' => 'required|string|min:6',
        ]);

        if ($request->hasFile('logo')) {
            $validator = Validator::make($request->all(), [
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        $merchant = Merchant::create([
            'name' => $request->name,
            'owner_id' => $request->user_id,
            'password' => $request->password,
            'logo' => $request->logo,
        ]);

        return response()->json([
            'message' => 'success',
            'status' => 201,
            'data' => $merchant
        ], 201);
    }
}
