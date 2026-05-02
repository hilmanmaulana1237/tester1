<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CacheStaticAssets;
use App\Http\Middleware\CompressResponse;
use App\Http\Middleware\EnsureUserNotBanned;
use App\Http\Middleware\EnsureUserCanTakeTask;
use App\Http\Middleware\TrustProxies;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust proxies for HTTPS/Cloudflare
        $middleware->web(prepend: [
            TrustProxies::class,
            CacheStaticAssets::class,
        ]);

        // Add GZIP compression as the last middleware (after response is generated)
        $middleware->web(append: [
            CompressResponse::class,
        ]);

        // Register middleware aliases for route-specific use
        $middleware->alias([
            'not-banned' => EnsureUserNotBanned::class,
            'can-take-task' => EnsureUserCanTakeTask::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
