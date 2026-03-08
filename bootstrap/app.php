<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'stripe.enabled' => \App\Http\Middleware\EnsureStripeEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ADR-005: Return 404 instead of 403 to prevent resource enumeration
        $exceptions->render(function (AuthorizationException $e, $request) {
            Log::warning('Authorization denied (returned as 404)', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            throw new NotFoundHttpException('Not Found');
        });
    })->create();
