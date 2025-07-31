<?php

use App\Http\Controllers\Application\Auth\AuthController;
use App\Http\Controllers\Application\Auth\ResetPasswordController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth:sanctum', 'abilities:verified']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::apiResource('categories', CategoryController::class);









