<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MerchantMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required|exists:merchant_menu_list,id',
            'quantity' => 'required|integer|min:1',
            'order_type' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:qris,cash',
            'delivery_location' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $menu = MerchantMenu::find($request->menu_id);
        $subtotal = $menu->price * $request->quantity;

        $order = Order::create([
            'user_id' => Auth::id(), // pastikan user login pakai JWT
            'menu_id' => $menu->id,
            'quantity' => $request->quantity,
            'subtotal' => $subtotal,
            'scheduled_time' => $request->scheduled_time,
            'order_type' => $request->order_type,
            'payment_method' => $request->payment_method,
            'delivery_location' => $request->order_type === 'delivery' ? $request->delivery_location : null,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => $order
        ], 201);
    }
    public function allOrders()
    {
        $orders = Order::with('user')->latest()->get(); // Optional: ambil relasi user
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
