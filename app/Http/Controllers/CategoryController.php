<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Facades\ApiResponse;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Dashboard\Categories\CategoryStoreRequest;
use App\Http\Requests\Dashboard\Categories\CategoryUpdateRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return ApiResponse::success(CategoryResource::collection($categories));
    }

    public function store(CategoryStoreRequest $request, CategoryService $service)
    {
        try {
            $category = $service->create($request->validated());
            return ApiResponse::created(new CategoryResource($category));
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function show(Category $category)
    {
        return ApiResponse::success(new CategoryResource($category));
    }

    public function update(CategoryUpdateRequest $request, Category $category, CategoryService $service)
    {
        try {
            $category = $service->update($category, $request->validated());
            return ApiResponse::updated(new CategoryResource($category));
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return ApiResponse::deleted();
    }
}
