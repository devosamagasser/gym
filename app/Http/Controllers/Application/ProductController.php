<?php

namespace App\Http\Controllers\Application;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service)
    {
    }

    public function index(Request $request)
    {
        $filter = array_merge($request->all(), ['is_active' => true]);

        $limit = $request->query('limit', 10);

        $products = $this->service->list($filter, $limit);

        return ApiResponse::success(ProductResource::collection($products)->resource);
    }

    public function show(string $id)
    {
        try {
            $product = $this->service->find($id, true);
            $product->load(['translations', 'category', 'brand']);
            return ApiResponse::success(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Product not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}

