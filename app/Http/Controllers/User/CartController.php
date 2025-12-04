<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCartRequest;
use App\Http\Requests\User\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Services\Cart\CartService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(protected CartService $cartService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getUserCart($request->user());
        return $this->success(CartResource::collection($cart), 'Cart Items', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartRequest $request)
    {
        try {
            $cart = $this->cartService->addToCart($request->user(), $request->validated());
            return $this->success(new CartResource($cart), 'Cart Item Added', 201);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 422;
            return $this->error($e->getMessage(), [], $code);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        try {
            $cart = $this->cartService->updateCartItem($cart, $request->validated());
            return $this->success(new CartResource($cart), 'Cart Item Updated', 200);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 422;
            return $this->error($e->getMessage(), [], $code);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $this->cartService->removeCartItem($cart);
        return $this->success(null, 'Cart Item Removed', 200);
    }
}
