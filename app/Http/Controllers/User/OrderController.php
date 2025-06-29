<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\User\MenuRepository;
use App\Http\Repositories\User\OrderRepository;

class OrderController extends Controller
{
    public function getListTempOrder(Request $request) {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            $data = OrderRepository::getListTempOrder($user->id);

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

    public function createTempOrder(Request $request) {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|numeric|exists:merchant_menu_list,id',
            'quantity' => 'required|numeric',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $menuId = intval($request->menu_id);
        $quantity = intval($request->quantity);

        try {
            $user = $request->get('auth_user');
            $menuData = MenuRepository::getMenuDetail($menuId);
            $totalPrice = $menuData['menu_price'] * $quantity;

            $data = [
                'user_id' => $user->id,
                'merchant_id' => $menuData['merchant_id'],
                'menu_id' => $menuId,
                'price' => $totalPrice,
                'quantity' => $quantity,
                'notes' => $request->notes
            ];

            OrderRepository::createTempOrder($data);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function createOrder(Request $request) {
        $user = $request->get('auth_user');

        $validateId = Validator::make(['user_id' => $user->id], [
            'user_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required|numeric|exists:users,id',
            'location_id' => 'required_if:type,1|numeric|exists:locations,id',
            'type' => 'required|numeric|in:1,2,3',
            'payment_method' => 'required|numeric|in:1,2',
            'is_preorder' => 'required|boolean',
            'schedule' => 'required_if:is_preorder,1|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userOrder = OrderRepository::getListTempOrder($user->id);
            $totalPrice = 0;

            foreach ($userOrder as $order) {
                $totalPrice += $order->price;
            }

            $data = [
                'user_id' => $user->id,
                'merchant_id' => $request->merchant_id,
                'location_id' => $request->location_id,
                'bill' => $totalPrice,
                'type' => $request->type,
                'payment_method' => $request->payment_method,
                'status' => 1,
                'schedule' => $request->schedule ?: null,
                'is_preorder' => $request->is_preorder,
                'order_list' => $userOrder
            ];

            OrderRepository::createOrder($data);
            OrderRepository::removeUserTempOrder($user->id);

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => null,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function updateTempOrder(Request $request, $tempOrderId) {
        $validateId = Validator::make(['temp_order_id' => $tempOrderId], [
            'temp_order_id' => 'exists:temp_user_order,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tempOrderDetail = OrderRepository::getTempOrderDetail($tempOrderId);
            $menuData = MenuRepository::getMenuDetail($tempOrderDetail->menu_id);
            $totalPrice = $menuData['menu_price'] * $request->quantity;

            $data = [
                'temp_order_id' => $tempOrderId,
                'price' => $totalPrice,
                'quantity' => $request->quantity,
                'notes' => $request->notes ?? null,
            ];

            OrderRepository::updateTempOrder($data);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function removeTempOrder($tempOrderId) {
        $validator = Validator::make(['temp_order_id' => $tempOrderId], [
            'temp_order_id' => 'exists:temp_user_order,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            OrderRepository::removeTempOrder($tempOrderId);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }
}
