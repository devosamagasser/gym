<?php

use App\Facades\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //a
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiResponse::unauthorized();
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            return ApiResponse::forbidden();
        });

        $exceptions->render(function (UnauthorizedException $e, $request) {
            return ApiResponse::unauthorized();
        });


    })->create();
