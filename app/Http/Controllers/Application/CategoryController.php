<?php

namespace App\Http\Controllers\Application;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
    }

    public function index(Request $request)
    {   
        $filter = array_merge($request->all(), ['is_active' => true]);

        $limit = $request->query('limit', 10);

        $categories = $this->service->list($filter, $limit);
        return ApiResponse::success(CategoryResource::collection($categories)->resource);
    }

    public function show(string $id)
    {
        try {
            $category = $this->service->find($id, true);
            $category->loadCount('products');
            $category->load('products');
            $category->load('translations');
            return ApiResponse::success(new CategoryResource($category));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Category not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

}
