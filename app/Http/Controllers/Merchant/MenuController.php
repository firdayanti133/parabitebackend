<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\Merchant\MenuRepository;

class MenuController extends Controller
{
    public function getMenuDetail($menu_id) {
        $validateId = Validator::make(['menu_id' => $menu_id], [
            'menu_id' => '|numeric|max:255|exists:merchant_menu_list,id',    
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            $data = MenuRepository::getMenuDetail($menu_id);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function createMenu(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|string|max:255',
            'nutrition_facts' => 'required|string|max:255',
            'price' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

       $user = $request->get('auth_user');

        $validateId = Validator::make(['id' => $user->id], [
            'id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            $photoPath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $path = $file->move('storage/img/menu', $filename);
                $photoPath = $path;
            }

            $data = [
                'merchant_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $photoPath,
                'type' => $request->type,
                'nutrition_facts' => $request->nutrition_facts,
                'price' => $request->price
            ];

            MenuRepository::createMenu($data);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function updateMenu(Request $request, $menu_id) {
        $merchant = $request->get('auth_user');

        $validateId = Validator::make(['merchant_id' => $merchant->id], [
            'merchant_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        $validateId = Validator::make(['menu_id' => $menu_id], [
            'menu_id' => 'exists:merchant_menu_list,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|string|max:255|in:1,2',
            'nutrition_facts' => 'required|string|max:255',
            'price' => 'required|numeric|max:255',
            'status' => 'required|string|max:255|in:1,2,3',
            'is_favorite' => 'required|string|max:255|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $photoPath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $path = $file->move('storage/img/menu', $filename);
                $photoPath = $path;
            }

            $data = [
                'id' => $menu_id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $photoPath,
                'type' => $request->type,
                'nutrition_facts' => $request->nutrition_facts,
                'price' => $request->price,
                'status' => $request->status,
                'is_favorite' => $request->is_favorite
            ];

            MenuRepository::updateMenu($data);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function deleteMenu (Request $request, $menu_id) {
        $merchant = $request->get('auth_user');

        $validateId = Validator::make(['merchant_id' => $merchant->id], [
            'merchant_id' => 'exists:users,id',
        ]);
        
        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        $validateId = Validator::make(['menu_id' => $menu_id], [
            'menu_id' => 'exists:merchant_menu_list,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            MenuRepository::deleteMenu($menu_id);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }
}
