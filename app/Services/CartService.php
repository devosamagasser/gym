<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{
    public function list(int $userId): Collection
    {
        return Cart::where('user_id', $userId)
            ->with('product')
            ->get();
    }

    public function add(int $userId, array $data): Cart
    {
        return DB::transaction(function () use ($userId, $data) {
            $quantity = $data['quantity'] ?? 1;
            $cart = Cart::where('user_id', $userId)
                        ->where('product_id', $data['product_id'])
                        ->first();

            if ($cart) {
                $cart->increment('quantity', $quantity);
                $cart->refresh();
            } else {
                $data['user_id'] = $userId;
                $data['quantity'] = $quantity;
                $cart = Cart::create($data);
            }

            return $cart->load('product');
        });
    }

    public function update(Cart $cart, array $data): Cart
    {
        $cart->update($data);
        return $cart->load('product');
    }

    public function delete(Cart $cart): void
    {
        $cart->delete();
    }

    public function findForUser(int $id, int $userId): Cart
    {
        $cart = Cart::where('user_id', $userId)->where('id', $id)->first();
        if (! $cart) {
            throw new ModelNotFoundException("Cart item not found.");
        }
        return $cart;
    }
}
