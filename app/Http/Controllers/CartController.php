<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        $total = $this->cartService->getTotal();
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $this->cartService->addToCart($productId, $request->input('quantity', 1));
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, $productId)
    {
        $this->cartService->updateQuantity($productId, $request->input('quantity'));
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
    }

    public function remove($productId)
    {
        $this->cartService->removeFromCart($productId);
        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }
}
