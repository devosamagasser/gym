<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\OrderController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:admins'])->group(function () {
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::apiResource('admins', AdminsController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::post('categories/{category}/cover', [CategoryController::class, 'updateCover']);
    Route::apiResource('brands', BrandController::class);
    Route::post('brands/{brand}/cover', [BrandController::class, 'updateCover']);
    Route::apiResource('products', ProductController::class);
    Route::post('brands/{brand}/cover', [ProductController::class, 'updateCover']);
    Route::post('brands/{brand}/gallery', [ProductController::class, 'updateGallery']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'update']);
});


