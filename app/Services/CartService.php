<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $cartKey = 'shopping_cart';

    public function getCart()
    {
        return Session::get($this->cartKey, []);
    }

    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->regular_price,
                'quantity' => $quantity,
                'image_url' => $product->image_url,
                'slug' => $product->slug,
            ];
        }

        Session::put($this->cartKey, $cart);
        return count($cart);
    }

    public function updateQuantity($productId, $quantity)
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
                Session::put($this->cartKey, $cart);
            } else {
                $this->removeFromCart($productId);
            }
        }
    }

    public function removeFromCart($productId)
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put($this->cartKey, $cart);
        }
    }

    public function clearCart()
    {
        Session::forget($this->cartKey);
    }

    public function getTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    public function getItemCount()
    {
        return count($this->getCart());
    }

    public function applyCoupon($couponCode)
    {
        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

        if (!$coupon || !$coupon->isValid()) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $subtotal = $this->getTotal();

        if ($subtotal < $coupon->min_purchase) {
            return ['success' => false, 'message' => 'Minimum purchase of ' . number_format($coupon->min_purchase, 2) . ' required.'];
        }

        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $coupon->calculateDiscount($subtotal),
        ]);

        return ['success' => true, 'message' => 'Coupon applied successfully!'];
    }

    public function removeCoupon()
    {
        Session::forget('applied_coupon');
    }

    public function getAppliedCoupon()
    {
        return Session::get('applied_coupon');
    }

    public function getTotalWithDiscount()
    {
        $subtotal = $this->getTotal();
        $coupon = $this->getAppliedCoupon();

        if ($coupon) {
            return max(0, $subtotal - $coupon['discount']);
        }

        return $subtotal;
    }
}
