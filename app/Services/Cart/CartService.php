<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CartService
{
    // Get user cart items
    public function getUserCart($user)
    {
        return $user->cart()->with('product')->get();
    }

    // Add item to cart
    public function addToCart($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $product = Product::where('id', $data['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($product->stock < $data['quantity']) {
                throw new \Exception('Insufficient product stock', 400);
            }

            $cart = Cart::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $data['product_id'],
                ],
                [
                    'quantity' => $data['quantity'],
                ]
            );

            return $cart->load('product');
        });
    }

    // Update cart item quantity
    public function updateCartItem(Cart $cart, array $data)
    {
        Gate::authorize('update', $cart);

        return DB::transaction(function () use ($cart, $data) {
            $product = $cart->product()->lockForUpdate()->firstOrFail();

            if ($product->stock < $data['quantity']) {
                throw new \Exception('Insufficient product stock', 400);
            }

            $cart->update([
                'quantity' => $data['quantity'],
            ]);

            return $cart->load('product');
        });
    }

    // Remove cart item
    public function removeCartItem(Cart $cart)
    {
        Gate::authorize('delete', $cart);
        $cart->delete();
    }
}
