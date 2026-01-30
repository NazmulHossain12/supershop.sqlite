<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();

        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $this->cartService->getTotal();
        return view('checkout.index', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();

        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,card,paypal',
        ]);

        $grandTotal = $this->cartService->getTotal();

        // Create Order
        $order = Order::create([
            'user_id' => Auth::id(), // Nullable
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'grand_total' => $grandTotal,
            'item_count' => count($cart),
            'is_paid' => false, // Default for COD
            'payment_method' => $validated['payment_method'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'zip_code' => $validated['zip_code'],
            'notes' => $request->input('notes'),
        ]);

        // Create Order Items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['sale_price'] ?? $item['regular_price'],
            ]);

            // Update product stock
            $product = \App\Models\Product::find($item['id']);
            if ($product) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        // Clear cart
        session()->forget('cart');

        // Record transaction
        \App\Models\Transaction::create([
            'order_id' => $order->id,
            'type' => 'sale',
            'category' => 'product_sale',
            'amount' => $order->grand_total,
            'description' => 'Order #' . $order->order_number,
            'transaction_date' => now(),
        ]);

        // Send confirmation email
        Mail::to($order->email)->send(new OrderConfirmation($order));

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully! Order #' . $order->order_number);
    }
}
