<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\Merchant\MenuRepository;
use App\Http\Repositories\Merchant\ReportRepository;

class DashboardController extends Controller
{
    public function getListMenu(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'limit' => 'numeric',
            'type' => 'string|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

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

        $page = intval($request->page) ?? 1;
        $limit = intval($request->limit) ?? 10;
        $type = $request->type ?? null;

        if ($page < 1 || $limit < 1) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => 'Page and limit must be greater than 0'
            ], 422);
        }

        if ($page < 1 || $limit < 1) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => 'Page and limit must be greater than 0'
            ], 422);
        }

        try {
            $data = MenuRepository::getListMenu($merchant->id, $page, $limit, $type);

            $totalData = MenuRepository::countListMenu($merchant->id, $type);

            $totalPage = ceil($totalData / $limit);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'total_data' => $totalData,
                    'page' => $page,
                    'limit' => $limit,
                    'total_page' => $totalPage,
                    'data' => $data
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function getFavoriteMenu(Request $request) {
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

        try {
            $data = MenuRepository::getFavoriteMenu($merchant->id);

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

    public function checkDailyIncome(Request $request) {
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

        try {
            $data = ReportRepository::checkDailyIncome($merchant->id);

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
}
