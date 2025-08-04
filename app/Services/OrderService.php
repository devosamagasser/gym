<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    public function list(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(int $userId, array $data): Order
    {
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        if ($cartItems->isEmpty()) {
            throw new ModelNotFoundException('Cart is empty.');
        }

        return DB::transaction(function () use ($userId, $data, $cartItems) {
            $total = 0;
            foreach ($cartItems as $item) {
                $price = $item->product->price - $item->product->sale;
                if ($item->product->stock < $item->quantity) {
                    throw new ModelNotFoundException('Insufficient stock for product '.$item->product_id);
                }
                $total += $price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $total,
                'payment_method' => $data['payment_method'],
            ]);

            foreach ($cartItems as $item) {
                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'sale' => $item->product->sale,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            Cart::where('user_id', $userId)->delete();

            return $order->load('products');
        });
    }

    public function find(int $id, int $userId): Order
    {
        $order = Order::where('user_id', $userId)
            ->with('products')
            ->find($id);
        if (! $order) {
            throw new ModelNotFoundException('Order not found.');
        }
        return $order;
    }

    public function adminList(): Collection
    {
        return Order::with(['user', 'products'])->latest()->get();
    }

    public function adminFind(int $id): Order
    {
        $order = Order::with(['user', 'products'])->find($id);
        if (! $order) {
            throw new ModelNotFoundException('Order not found.');
        }
        return $order;
    }

    public function updateStatus(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->refresh()->load(['user', 'products']);
    }
}
