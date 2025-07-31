<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AdminsController;
use App\Http\Controllers\Dashboard\AuthController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:admins'])->group(function () {
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::apiResource('admins', AdminsController::class);
});


