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
            'user_id' => 'required|exists:users,id',
            'order_type' => 'required|in:delivery,takeaway,dine-in',
            'location_id' => 'required|exists:locations,id',
            'payment_method' => 'required|in:qris,cash',
            'total_price' => 'required|integer|min:1',
            'menu_id' => 'required|array|exists:merchant_menu_list,id',
            'quantity' => 'required|array|min:1',
            "is_preorder"=> 'required|boolean',
            "preorder_timeset" => 'required_if:is_preorder,true',
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

        $menu = MerchantMenu::where('id', $request->menu_id)->first();

        $order = Order::create([
            'user_id' => $request->user_id, 
            'merchant_id' => $menu->merchant_id,
            'location_id' => $request->location_id,
            'total_price' => $request->total_price,
            'order_type' => $request->order_type,
            'payment_method' => $request->payment_method,
            'status' => 'waiting',
            'is_preorder' => $request->is_preorder,
            'preorder_timeset' => $request->preorder_timeset
        ]);

        foreach ($request->menu_id as $menuId) {
            OrderList::create([
                'order_id' => $order->id,
                'menu_id' => $menuId,
                'quantity' => $request->quantity[array_search($menuId, $request->menu_id)],
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
