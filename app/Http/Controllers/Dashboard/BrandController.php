<?php

namespace App\Http\Controllers\Dashboard;

use App\Facades\ApiResponse;
use App\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Dashboard\Brands\StoreBrandRequest;
use App\Http\Requests\Dashboard\Brands\UpdateBrandRequest;
use App\Http\Requests\Dashboard\Brands\UpdateBrandCoverRequest;

class BrandController extends Controller
{
    public function __construct(protected BrandService $service)
    {
    }

    public function index()
    {
        $brands = $this->service->list();
        return ApiResponse::success(BrandResource::collection($brands)->resource);
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = $this->service->create($request->validated());
            return ApiResponse::created(new BrandResource($brand));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $brand = $this->service->find($id);
            $brand->loadCount('products');
            $brand->load('translations');
            return ApiResponse::success(new BrandResource($brand));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Brand not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(UpdateBrandRequest $request, string $id)
    {
        try {
            $brand = $this->service->find($id);
            $brand = $this->service->update($brand, $request->validated());
            return ApiResponse::updated(new BrandResource($brand));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Brand not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function updateCover(UpdateBrandCoverRequest $request, string $id)
    {
        try {
            $brand = $this->service->find($id);
            $brand = $this->service->updateCover($brand, $request->validated()['cover']);
            return ApiResponse::message('Cover updated successfully.');
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Brand not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $brand = $this->service->find($id);
            $this->service->delete($brand);
            return ApiResponse::deleted();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Brand not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
