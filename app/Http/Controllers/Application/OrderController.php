<?php

namespace App\Http\Controllers\Application;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service) {}

    public function index()
    {
        $orders = $this->service->list(auth()->id());
        return ApiResponse::success(OrderResource::collection($orders)->resource);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->service->create(auth()->id(), $request->validated());
            return ApiResponse::created(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::failed(null, $e->getMessage(), 404);
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $order = $this->service->find($id, auth()->id());
            return ApiResponse::success(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Order not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
