<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AdminsController;

Route::post('login', [AdminsController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::delete('logout', [AdminsController::class, 'logout']);
    Route::get('dashboard', [AdminsController::class, 'dashboard']);
});


