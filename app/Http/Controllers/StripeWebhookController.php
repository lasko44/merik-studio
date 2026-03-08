<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Handles incoming Stripe webhook events.
 */
class StripeWebhookController extends Controller
{
    /**
     * Handle the incoming webhook request.
     */
    public function __invoke(Request $request, StripeService $stripeService): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (!$sigHeader) {
            return response('Missing signature header', 400);
        }

        $event = $stripeService->constructWebhookEvent($payload, $sigHeader);

        if (!$event) {
            return response('Invalid webhook signature', 400);
        }

        $stripeService->handleWebhook($event);

        return response('Webhook handled', 200);
    }
}
