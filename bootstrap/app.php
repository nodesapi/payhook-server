<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            $domain = env('APP_DOMAIN', 'cekbayar.com');

            // Rute API (Subdomain api.cekbayar.com)
            \Illuminate\Support\Facades\Route::middleware('api')
                ->domain('api.' . $domain)
                ->prefix('api') // URL menjadi api.cekbayar.com/api/...
                ->group(base_path('routes/api.php'));

            // Rute Web Publik & Dashboard (Domain Utama cekbayar.com)
            \Illuminate\Support\Facades\Route::middleware('web')
                ->domain($domain)
                ->group(base_path('routes/web.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Server Error',
                ], $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ? $e->getStatusCode() : 500);
            }
        });
    })->create();
