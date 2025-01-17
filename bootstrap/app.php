<?php

use App\Http\Middleware\LoadUserData;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            LoadUserData::class, // Nuestro middleware
        ]);
        // $middleware->api([
        //     \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        //     LoadUserData::class,
        // ]);

        // // Configurar el rate limiter
        // RateLimiter::for('api', function (Request $request) {
        //     return RateLimiter::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();