<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $cart = Cart::where('user_id', $userId)
                        ->where('product_id', $data['product_id'])
                        ->exists();

            if ($cart) {
                throw new HttpException(409, 'Product already exists in cart.');
            } 
            
            $product = ProductService::find($data['product_id']);

            if ($product->stock < $data['quantity']) {
                throw new HttpException(400, 'Requested quantity exceeds available stock.');
            }
            $data['user_id'] = $userId;
            $cart = Cart::create($data);
            

            return $cart->load('product');
        });
    }

    public function update(Cart $cart, array $data): Cart
    {
        if ($cart->product->stock < $data['quantity']) {
            throw new HttpException(400, 'Requested quantity exceeds available stock.');
        }
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
