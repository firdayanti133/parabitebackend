<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Merchant\MenuRepository;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function getMenuDetail($menuId)
    {
        $validator = Validator::make(['menu_id' => $menuId], [
            'menu_id' => 'required|integer|exists:merchant_menu_list,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => MenuRepository::getMenuDetail($menuId),
            ]);
        } catch (\Throwable $exception) {
            return ApiResponse::fromException($exception, request());
        }
    }

    public function createMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|integer|in:1,2,3',
            'nutrition_facts' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $merchant = $request->get('auth_user');

        try {
            MenuRepository::createMenu([
                'merchant_id' => $merchant->id,
                'name' => trim($request->name),
                'description' => trim($request->description),
                'image' => $this->storeMenuImage($request),
                'type' => (int) $request->type,
                'nutrition_facts' => trim($request->nutrition_facts),
                'price' => (int) $request->price,
            ]);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null,
            ], 201);
        } catch (\Throwable $exception) {
            return ApiResponse::fromException($exception, $request);
        }
    }

    public function updateMenu(Request $request, $menuId)
    {
        $merchant = $request->get('auth_user');
        $menuIdValidator = Validator::make(['menu_id' => $menuId], [
            'menu_id' => 'required|integer|exists:merchant_menu_list,id',
        ]);

        if ($menuIdValidator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $menuIdValidator->errors(),
            ], 422);
        }

        if (! MenuRepository::belongsToMerchant($menuId, $merchant->id)) {
            return response()->json([
                'code' => 403,
                'message' => 'Forbidden: Menu does not belong to this merchant',
                'errors' => null,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|integer|in:1,2,3',
            'nutrition_facts' => 'required|string|max:255',
            'price' => 'required|integer|min:1',
            'status' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $menu = MenuRepository::getMenuDetail($menuId);
            $photoPath = $request->hasFile('image')
                ? $this->storeMenuImage($request)
                : $menu['image'];

            MenuRepository::updateMenu([
                'id' => $menuId,
                'name' => trim($request->name),
                'description' => trim($request->description),
                'image' => $photoPath,
                'type' => (int) $request->type,
                'nutrition_facts' => trim($request->nutrition_facts),
                'price' => (int) $request->price,
                'status' => (int) $request->status,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null,
            ]);
        } catch (\Throwable $exception) {
            return ApiResponse::fromException($exception, $request);
        }
    }

    public function deleteMenu(Request $request, $menuId)
    {
        $merchant = $request->get('auth_user');
        $validator = Validator::make(['menu_id' => $menuId], [
            'menu_id' => 'required|integer|exists:merchant_menu_list,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! MenuRepository::belongsToMerchant($menuId, $merchant->id)) {
            return response()->json([
                'code' => 403,
                'message' => 'Forbidden: Menu does not belong to this merchant',
                'errors' => null,
            ], 403);
        }

        try {
            MenuRepository::deleteMenu($menuId);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null,
            ]);
        } catch (\Throwable $exception) {
            return ApiResponse::fromException($exception, $request);
        }
    }

    private function storeMenuImage(Request $request): string
    {
        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->extension();
        $path = Storage::disk('public')->putFileAs('img/menu', $file, $filename);

        if (! $path) {
            throw new \RuntimeException('Unable to store the uploaded menu image');
        }

        return 'storage/'.$path;
    }
}
