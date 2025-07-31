<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Facades\ApiResponse;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
    }

    public function index()
    {
        $categories = $this->service->list(request()->all());
        return ApiResponse::success(CategoryResource::collection($categories));
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

    public function show(Category $category)
    {
        $category->loadCount('products');
        $category->load('translations');
        return ApiResponse::success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category = $this->service->update($category, $request->validated());
            return ApiResponse::updated(new CategoryResource($category));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->service->delete($category);
            return ApiResponse::deleted();
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }
}
