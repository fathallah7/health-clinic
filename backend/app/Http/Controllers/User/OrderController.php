<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{  
    use ApiResponse;

    public function index(Request $request)
    {
        $patient = $request->user();
        $orders = $patient->orders()->with('item.product')->latest()->get();
        return $this->success($orders, 'My Orders', 200);
    }

    public function checkout(Request $request)
    {
        $patient = $request->user();
        $cart = $patient->cart()->with('product')->get();
        $address = $patient->address()->first();

        if ($cart->isEmpty()) {
            return $this->error('Cart is empty', [], 400);
        }

        if (!$address) {
            return $this->error('No default address', [], 400);
        }

        DB::transaction(function () use ($patient, $cart, $address) {
            $total = $cart->sum(fn($i) => $i->quantity * $i->product->price);

            $order = Order::create([
                'user_id'      => $patient->id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $total,
                'address_id'   => $address->id,
                'status'       => 'pending',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'       => $order->id,
                    'product_id'     => $item->product_id,
                    'product_name'   => $item->product->name,
                    'product_price'  => $item->product->price,
                    'quantity'       => $item->quantity,
                    'subtotal'       => $item->quantity * $item->product->price,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            $patient->cart()->delete();
        });

        return $this->success(null, 'Order created successfully', 201);
    }
}
