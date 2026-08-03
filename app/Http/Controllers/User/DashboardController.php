<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Repositories\User\MenuRepository;
use App\Http\Repositories\User\MerchantRepository;
use App\Http\Repositories\User\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function getCurrentOrder(Request $request)
    {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors(),
            ], 422);
        }

        try {
            $data = OrderRepository::getCurrentOrder($user->id);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function getListMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'limit' => 'numeric',
            'type' => 'string|in:1,2,3',
            'search' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
        $search = $request->search ?? '';
        $type = $request->type ?? null;

        if ($page < 1 || $limit < 1) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => 'Page and limit must be greater than 0',
            ], 422);
        }

        try {
            $data = MenuRepository::getListMenu($page, $limit, $search, $type);

            $totalData = MenuRepository::countListMenu($search, $type);
            $totalPage = ceil($totalData / $limit);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'total_data' => $totalData,
                    'page' => $page,
                    'limit' => $limit,
                    'total_page' => $totalPage,
                    'data' => $data,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function getMerchantListMenu(Request $request, $merchant_id)
    {
        $validateId = Validator::make(['merchant_id' => $merchant_id], [
            'merchant_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors(),
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'limit' => 'numeric',
            'search' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
        $search = $request->search ?? '';

        if ($page < 1 || $limit < 1) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => 'Page and limit must be greater than 0',
            ], 422);
        }

        try {
            $data = MenuRepository::getMerchantListMenu($merchant_id, $page, $limit, $search);

            $totalData = MenuRepository::countMerchantListMenu($merchant_id, $search);
            $totalPage = ceil($totalData / $limit);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'total_data' => $totalData,
                    'page' => $page,
                    'limit' => $limit,
                    'total_page' => $totalPage,
                    'data' => $data,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function getSepuluhRibuMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'search' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = (int) $request->input('page', 1);
        $limit = (int) $request->input('limit', 10);
        $search = $request->input('search', '');

        try {
            $data = MenuRepository::getMenusUnderPrice($page, $limit, $search);
            $totalData = MenuRepository::countMenusUnderPrice($search);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'total_data' => $totalData,
                    'page' => $page,
                    'limit' => $limit,
                    'total_page' => (int) ceil($totalData / $limit),
                    'data' => $data,
                ],
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $exception,
            ], 500);
        }
    }

    public function getMenuDetail($menu_id)
    {
        $validateId = Validator::make(['menu_id' => $menu_id], [
            'menu_id' => '|numeric|max:255|exists:merchant_menu_list,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors(),
            ], 422);
        }

        try {
            $data = MenuRepository::getMenuDetail($menu_id);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function getMerchantList(Request $request)
    {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors(),
            ], 422);
        }

        try {
            $data = MerchantRepository::getMerchantList();

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }

    public function getRecommendedMenu(Request $request)
    {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors(),
            ], 422);
        }

        try {
            $data = MerchantRepository::getMerchantTopMenu();

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e,
            ], 500);
        }
    }
}
