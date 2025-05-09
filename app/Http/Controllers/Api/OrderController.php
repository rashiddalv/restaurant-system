<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Pagination (15 orders per page);
        $perPage = $request->input('per_page', 15);
        $orders = $query->paginate($perPage);

        return $orders;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => $request->input('user_id'),
            'total_amount' => 0,
            'status' => 'pending',
        ]);

        $total_amount = 0;

        foreach ($request->items as $itemData) {
            $menuItem = \App\Models\Menu::findOrFail($itemData['menu_id']);
            $price = $menuItem->price;
            $quantity = $itemData['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $itemData['menu_id'],
                'quantity' => $quantity,
                'price' => $price,
            ]);

            $total_amount += $price * $quantity;
        }
        $order->update(['total_amount' => $total_amount]);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {

        $order->load('orderItems.menu', 'user');

        return $order;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);

        $order->update($request->only('status'));

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
