<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application\BrandController;
use App\Http\Controllers\Application\CategoryController;
use App\Http\Controllers\Application\Auth\AuthController;
use App\Http\Controllers\Application\Auth\ResetPasswordController;
use App\Http\Controllers\Application\ProductController;

Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signUp']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('verify-email', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'abilities:not-verified']);
    
Route::prefix('password')->group(function (){
    Route::post('forgot', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('verification', [ResetPasswordController::class, 'resetPasswordVerification']);
    Route::put('reset', [ResetPasswordController::class, 'resetPassword'])
    ->middleware([
        'auth:sanctum', 
        'abilities:reset-password'
    ]);
});

showing('categories', CategoryController::class);
showing('brands', BrandController::class);
showing('products', ProductController::class);

Route::group(['middleware' => ['auth:sanctum', 'abilities:verified']], function () {
    Route::delete('logout', [AuthController::class, 'logout']);
});





function showing($module, $controller)
{
    Route::get($module, [$controller, 'index']);
    Route::get($module.'/{id}', [$controller, 'show']);
}










