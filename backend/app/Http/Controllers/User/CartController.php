<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $patient = $request->user();
        $cart = $patient->cart()->with('product')->get();
        return $this->success(CartResource::collection($cart), 'Cart Items', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $patient = $request->user();

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request, $patient) {
            $product = Product::where('id', $request->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($product->stock < $request->quantity) {
                return $this->error('Insufficient product stock', [], 400);
            }

            $cart = Cart::updateOrCreate(
                [
                    'user_id' => $patient->id,
                    'product_id' => $request->product_id,
                ],
                [
                    'quantity' => $request->quantity,
                ]
            );

            return $this->success(new CartResource($cart->load('product')), 'Cart Item Added', 201);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        if (Gate::denies('update', $cart)) {
            return $this->error('You do not have permission to update this cart item', [], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        return DB::transaction(function () use ($request, $cart) {
            $product = $cart->product()->lockForUpdate()->firstOrFail();

            if ($product->stock < $request->quantity) {
                return $this->error('Insufficient product stock', [], 400);
            }

            $cart->update([
                'quantity' => $request->quantity,
            ]);

            return $this->success(new CartResource($cart->load('product')), 'Cart Item Updated', 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        if (Gate::denies('delete', $cart)) {
            return $this->error('You do not have permission to delete this cart item', [], 403);
        }

        $cart->delete();
        return $this->success(null, 'Cart Item Removed', 200);
    }
}
