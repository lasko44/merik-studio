<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Ensures Stripe functionality is enabled before allowing access.
 */
class EnsureStripeEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('cms.stripe_enabled', false)) {
            throw new NotFoundHttpException();
        }

        return $next($request);
    }
}
