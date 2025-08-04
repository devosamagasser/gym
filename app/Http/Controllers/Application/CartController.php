<?php

namespace App\Http\Controllers\Application;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    public function __construct(protected CartService $service)
    {
    }

    public function index()
    {
        $items = $this->service->list(auth()->id());
        $total = $items->sum(function ($item) {
            $price = $item->product->sale ?? $item->product->price;
            return $price * $item->quantity;
        });
        return ApiResponse::success([
            'items' => CartResource::collection($items)->resource,
            'total_price' => $total,
        ]);
    }

    public function store(StoreCartRequest $request)
    {
        try {
            $cart = $this->service->add(auth()->id(), $request->validated());
            return ApiResponse::created(new CartResource($cart));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(UpdateCartRequest $request, $id)
    {
        try {
            $cart = $this->service->findForUser($id, auth()->id());
            $cart = $this->service->update($cart, $request->validated());
            return ApiResponse::updated(new CartResource($cart));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Cart item not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $cart = $this->service->findForUser($id, auth()->id());
            $this->service->delete($cart);
            return ApiResponse::deleted();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Cart item not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
