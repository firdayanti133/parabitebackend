<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MerchantMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderList;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|array|min:1',
            'order_type' => 'required|in:delivery,takeaway,dine-in',
            'payment_method' => 'required|in:qris,cash',
            'merchant_id' => 'required|exists:merchants,id',
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'total_price' => 'required|integer|min:1',
            'food_id' => 'required|array|exists:foods,id',
            
        ]);

        foreach ($request->menu_id as $menuId) {
            $validator = Validator::make(['menu_id' => $menuId], [
                'menu_id' => 'required|exists:merchant_menu_list,id',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
       
        $order = Order::create([
            'user_id' => $request->user_id, 
            'merchant_id' => $request->merchant_id,
            'scheduled_time' => $request->scheduled_time, 
            'order_type' => $request->order_type,
            'payment_method' => $request->payment_method,
            'location_id' => $request->location_id,
            'total_price' => $request->total_price
        ]);

        foreach ($request->menu_id as $menuId) {
            OrderList::create([
                'order_id' => $order->id,
                'menu_id' => $menuId,
                'quantity' => $request->quantity[$menuId],
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => $order
        ], 201);
    }

    public function allOrders()
    {
        $orders = Order::with('user')->latest()->get();
        return response()->json([
            'message' => 'List of all orders',
            'data' => $orders
        ]);
    }

    public function getOrder($id)
    {
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'message' => 'Order detail',
            'data' => $order
        ]);
    }

    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'Order status updated',
            'data' => $order
        ]);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    
}
