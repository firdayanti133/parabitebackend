<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderList;
use Illuminate\Support\Facades\Validator;
use Exception;

class OrderController extends Controller
{
    public function getMerchantOrders($merchant_id) {
        $orders = Order::where('merchant_id', $merchant_id)->get();
        return response()->json([
            'message' => 'success',
            'status' => 200,
            'data' => $orders
        ], 200);
    }

    public function updateMerchantOrders(Request $request, $id) {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $validator = Validator::make(['status' => $request->status], [
            'status' => 'required|string|in:waiting,confirmed,processing,done,cancelled',
        ]);

        $data['status'] = $request->status;

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::where('id', $id)->first();
            $order->update($data);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $order
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
