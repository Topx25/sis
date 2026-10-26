<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '', // served from the /api subfolder docroot, so routes keep their v1/... paths (URL => /api/v1/...)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \App\Http\Middleware\RedirectMiddleware::class,
        ]);

        // Resolve locale from Accept-Language header for all API requests
        $middleware->api(prepend: [
            \App\Http\Middleware\ExtractAdminToken::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Cache public read responses as static JSON for the web server to serve directly
        $middleware->api(append: [
            \App\Http\Middleware\CachePublicApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('v1/*'),
        );
    })->create();
