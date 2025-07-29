<?php

namespace App\Providers;

use App\Facades\ApiResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Facades\FacadesLogic\ApiResponseLogic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ApiResponse::class,
            ApiResponseLogic::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api/dashboard')
            ->group(base_path('routes/dashboard.php'));
    }
}