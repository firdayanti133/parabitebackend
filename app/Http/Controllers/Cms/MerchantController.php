<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\MerchantMenu;
use App\Models\Food;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $data = [
            'owner_id' => $request->user_id,
            'name' => $request->name,
        ];

        try {
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $path = $file->move(public_path('/images/merchant'), str_replace(' ', '_', $fileName));
                $data['logo'] = $path;
            }
    
            $data['password'] = Hash::make($request->password);
    
            Merchant::create($data);
    
            return response()->json([
                'message' => 'success',
                'status' => 201,
                'data' => null
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error',
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function updateMerchant(Request $request, $id) {
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
            'name' => 'required|string|max:255',
            'owner_id' => 'required|integer|exists:users,id',
            'password' => 'sometimes|string|min:6',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $data = [
            'id' => $request->id,
            'owner_id' => $request->owner_id,
            'name' => $request->name,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $path = $file->move(public_path('/images/merchant'), str_replace(' ', '_', $fileName));
                $data['logo'] = $path;
            }

            $merchant = Merchant::where('id', $request->id)->first();
            $merchant->update($data);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => null
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'error: ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ], 500);
        }
    } 

    public function deleteMerchant($id) {
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

        try {
            $merchant = Merchant::where('id', $id)->first();
            $merchant->delete();

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
