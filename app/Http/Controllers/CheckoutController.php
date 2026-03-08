<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CreateCheckoutSessionRequest;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles Stripe Checkout session creation and redirects.
 */
class CheckoutController extends Controller
{
    /**
     * Create a new Stripe Checkout session.
     */
    public function createSession(CreateCheckoutSessionRequest $request, StripeService $stripeService): JsonResponse
    {
        if (!$stripeService->isConfigured()) {
            return response()->json([
                'error' => 'Payment processing is not available.',
            ], 503);
        }

        $session = $stripeService->createCheckoutSessionFromPriceId(
            $request->input('price_id'),
            $request->getQuantity(),
            $request->getSuccessUrl(),
            $request->getCancelUrl()
        );

        if (!$session) {
            return response()->json([
                'error' => 'Unable to create checkout session.',
            ], 500);
        }

        return response()->json([
            'session_id' => $session->id,
            'url' => $session->url,
        ]);
    }

    /**
     * Handle successful checkout redirect.
     */
    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        return redirect('/')
            ->with('success', 'Thank you for your purchase!')
            ->with('checkout_session_id', $sessionId);
    }

    /**
     * Handle cancelled checkout redirect.
     */
    public function cancel(): RedirectResponse
    {
        return redirect('/')
            ->with('info', 'Checkout was cancelled.');
    }
}
