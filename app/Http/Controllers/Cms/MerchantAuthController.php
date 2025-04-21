<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class MerchantAuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'owner_id' => $request->user_id,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ];

        try {
            if ($request->file('logo')) {
                $file = $request->file('logo');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $path = $file->move(public_path('/images/merchant'), str_replace(' ', '_', $fileName));
                $data['logo'] = $path;
            }

            Merchant::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error' . $e,
                'status' => 500,
            ], 500);
        }

        return response()->json([
            'message' => 'Merchant successfully registered',
            'status' => 201,
        ], 200); 
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|exists:merchants,name',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('name', 'password');

        try {
            $credentials = $request->only('name', 'password');

            // Find the merchant by name
            $merchant = Merchant::where('name', $credentials['name'])->first();
        
            if (!$merchant) {
                return response()->json([
                    'message' => 'Merchant Not Found',
                    'status' => 404,
                ], 404);
            }
        
            // Validate the password
            if (!Hash::check($credentials['password'], $merchant->password)) {
                return response()->json([
                    'message' => 'Invalid Password',
                    'status' => 401,
                ], 401);
            }
        
            // Assign the role explicitly
            $merchant->role = 'merchant'; // Ensure the role is set
        
            // Define custom claims for the JWT token
            $customClaims = [
                'exp' => now()->addDays(3)->timestamp, // Token expiration time
                'role' => $merchant->role, // Include the role in the token
            ];

            $token = JWTAuth::claims($customClaims)->fromUser($merchant);

            return response()->json([
                'message' => 'Merchant successfully logged in',
                'status' => 200,
                'token' => $token,
                'token_type' => 'bearer',
                'merchant' => $merchant
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'error' . $e,
                'status' => 500,
            ], 500);
        }
    }
}
