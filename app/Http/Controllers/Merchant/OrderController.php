<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\Merchant\OrderRepository;

class OrderController extends Controller
{
    public function getListOrder(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'limit' => 'numeric',
            'status' => 'string|in:1,2,3',
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

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
        $status = $request->status ?? null;

        if ($page < 1 || $limit < 1) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => 'Page and limit must be greater than 0'
            ], 422);
        }

        try {
            $data = OrderRepository::getListOrder($merchant->id, $page, $limit, $status);

            $totalData = OrderRepository::countListOrder($merchant->id, $status);
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

    public function getOrderDetail($order_id) {
        $validator = Validator::make(['order_id' => $order_id], [
            'order_id' => 'exists:user_orders,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = OrderRepository::getOrderDetail($order_id);

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

    public function updateOrderStatus(Request $request, $order_id) {
        $validator = Validator::make(['order_id' => $order_id], [
            'order_id' => 'exists:user_orders,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            OrderRepository::updateOrderStatus($order_id, $request->status);

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
