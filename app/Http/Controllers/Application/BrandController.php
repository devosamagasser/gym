<?php

namespace App\Http\Controllers\Application;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandController extends Controller
{
    public function __construct(protected BrandService $service)
    {
    }

    public function index(Request $request)
    {
        $filter = array_merge($request->all(), ['is_active' => true]);

        $limit = $request->query('limit', 10);

        $brands = $this->service->list($filter, $limit);
        return ApiResponse::success(BrandResource::collection($brands)->resource);
    }

    public function show(string $id)
    {
        try {
            $brand = $this->service->find($id, true);
            $brand->loadCount('products');
            $brand->load('translations');
            return ApiResponse::success(new BrandResource($brand));
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Brand not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

}
