<?php

namespace App\Http\Controllers\Dashboard;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Orders\UpdateOrderStatusRequest;
use App\Http\Resources\Dashboard\OrderResource;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $service)
    {
    }

    public function index()
    {
        $orders = $this->service->adminList();
        return ApiResponse::success(OrderResource::collection($orders)->resource);
    }

    public function show($id)
    {
        try {
            $order = $this->service->adminFind($id);
            return ApiResponse::success(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Order not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(UpdateOrderStatusRequest $request, $id)
    {
        try {
            $order = $this->service->adminFind($id);
            $order = $this->service->updateStatus($order, $request->validated());
            return ApiResponse::updated(new OrderResource($order));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Order not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
