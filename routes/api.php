<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application\UsersController;

Route::post('signup', [UsersController::class, 'signUp']);

Route::post('login', [UsersController::class, 'login']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('resend-otp', [UsersController::class, 'resendOtp']);
    Route::post('verify-email', [UsersController::class, 'verifyEmail']);
    Route::get('profile', [UsersController::class, 'profile']);
});






