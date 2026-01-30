<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,declined',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Send status update email
        if ($oldStatus !== $order->status) {
            \Illuminate\Support\Facades\Mail::to($order->email)->send(new \App\Mail\OrderStatusUpdate($order, $oldStatus));
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully!');
    }
}
