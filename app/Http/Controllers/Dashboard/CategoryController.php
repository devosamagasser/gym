<?php

namespace App\Http\Controllers\Dashboard;

use App\Facades\ApiResponse;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Dashboard\Categories\StoreCategoryRequest;
use App\Http\Requests\Dashboard\Categories\UpdateCategoryRequest;
use App\Http\Requests\Dashboard\Categories\UpdateCategoryCoverRequest;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
    }

    public function index()
    {   
        $categories = $this->service->list();
        return ApiResponse::success(CategoryResource::collection($categories)->resource);
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->service->create($request->validated());
            return ApiResponse::created(new CategoryResource($category));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $category = $this->service->find($id);
            $category->loadCount('products');
            $category->load('translations');
            return ApiResponse::success(new CategoryResource($category));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function update(UpdateCategoryRequest $request, string $id)
    {
        try {
            $category = $this->service->find($id);
            $category = $this->service->update($category, $request->validated());
            return ApiResponse::updated(new CategoryResource($category));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function updateCover(UpdateCategoryCoverRequest $request, string $id)
    {
        try {
            $category = $this->service->find($id);
            $category = $this->service->updateCover($category, $request->validated()['cover']);
            return ApiResponse::message('Cover updated successfully.');
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = $this->service->find($id);
            $this->service->delete($category);
            return ApiResponse::deleted();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
