<?php

namespace App\Http\Controllers\Dashboard;

use App\Facades\ApiResponse;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Dashboard\Products\StoreProductRequest;
use App\Http\Requests\Dashboard\Products\UpdateProductRequest;
use App\Http\Requests\Dashboard\Products\UpdateProductCoverRequest;
use App\Http\Requests\Dashboard\Products\UpdateProductGalleryRequest;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service)
    {
    }

    public function index()
    {
        $products = $this->service->list();
        return ApiResponse::success(ProductResource::collection($products)->resource);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->service->create($request->validated());
            return ApiResponse::created(new ProductResource($product));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $product = $this->service->find($id);
            $product->load(['translations', 'category', 'brand']);
            return ApiResponse::success(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Product not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        try {
            $product = $this->service->find($id);
            $product = $this->service->update($product, $request->validated());
            return ApiResponse::updated(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Product not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function updateCover(UpdateProductCoverRequest $request, string $id)
    {
        try {
            $product = $this->service->find($id);
            $product = $this->service->updateCover($product, $request->validated()['cover']);
            return ApiResponse::message('Cover updated successfully.');
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function updateGallery(UpdateProductGalleryRequest $request, string $id)
    {
        try {
            $product = $this->service->find($id);
            $product = $this->service->updateGalery($product, $request->validated()['gallery']);
            return ApiResponse::message('gallery updated successfully.');
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = $this->service->find($id);
            $this->service->delete($product);
            return ApiResponse::deleted();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Product not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}

