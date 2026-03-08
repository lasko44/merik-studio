<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Handles Stripe payment integration.
 */
class StripeService
{
    protected ?SiteSettings $settings = null;

    /**
     * Check if Stripe is enabled via feature flag.
     */
    public function isEnabled(): bool
    {
        return config('cms.stripe_enabled', false);
    }

    /**
     * Check if Stripe API keys are configured.
     */
    public function isConfigured(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $settings = $this->getSettings();

        return !empty($settings->stripe_publishable_key)
            && !empty($settings->stripe_secret_key);
    }

    /**
     * Get the publishable key for frontend use.
     */
    public function getPublishableKey(): ?string
    {
        return $this->getSettings()->stripe_publishable_key;
    }

    /**
     * Check if Stripe is in test mode.
     */
    public function isTestMode(): bool
    {
        return $this->getSettings()->stripe_test_mode;
    }

    /**
     * Create a Stripe Checkout session.
     */
    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl, array $options = []): ?Session
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $this->initStripe();

        try {
            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => $options['mode'] ?? 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ];

            if (isset($options['customer_email'])) {
                $sessionParams['customer_email'] = $options['customer_email'];
            }

            if (isset($options['metadata'])) {
                $sessionParams['metadata'] = $options['metadata'];
            }

            return Session::create($sessionParams);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session creation failed', [
                'error' => $e->getMessage(),
                'code' => $e->getStripeCode(),
            ]);

            return null;
        }
    }

    /**
     * Create a checkout session from a Stripe Price ID.
     */
    public function createCheckoutSessionFromPriceId(
        string $priceId,
        int $quantity,
        string $successUrl,
        string $cancelUrl,
        array $options = []
    ): ?Session {
        $lineItems = [
            [
                'price' => $priceId,
                'quantity' => $quantity,
            ],
        ];

        return $this->createCheckoutSession($lineItems, $successUrl, $cancelUrl, $options);
    }

    /**
     * Verify and construct a webhook event.
     */
    public function constructWebhookEvent(string $payload, string $sigHeader): ?\Stripe\Event
    {
        $settings = $this->getSettings();

        if (empty($settings->stripe_webhook_secret)) {
            Log::warning('Stripe webhook secret not configured');

            return null;
        }

        try {
            return Webhook::constructEvent(
                $payload,
                $sigHeader,
                $settings->stripe_webhook_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);

            return null;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Handle a webhook event.
     */
    public function handleWebhook(\Stripe\Event $event): void
    {
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }
    }

    /**
     * Test the Stripe API connection.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured. Please add your API keys.',
            ];
        }

        $this->initStripe();

        try {
            \Stripe\Balance::retrieve();

            return [
                'success' => true,
                'message' => 'Connection successful! Stripe is properly configured.',
                'test_mode' => $this->isTestMode(),
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create a Stripe product from a local Product model.
     */
    public function createProduct(Product $product): ?\Stripe\Product
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $this->initStripe();

        try {
            return \Stripe\Product::create([
                'name' => $product->name,
                'description' => $product->description ?? '',
                'metadata' => [
                    'local_product_id' => $product->id,
                ],
                'active' => $product->is_active,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe product creation failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
            ]);

            return null;
        }
    }

    /**
     * Update a Stripe product.
     */
    public function updateProduct(Product $product): ?\Stripe\Product
    {
        if (!$this->isConfigured() || !$product->stripe_product_id) {
            return null;
        }

        $this->initStripe();

        try {
            return \Stripe\Product::update($product->stripe_product_id, [
                'name' => $product->name,
                'description' => $product->description ?? '',
                'active' => $product->is_active,
                'metadata' => [
                    'local_product_id' => $product->id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe product update failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'stripe_product_id' => $product->stripe_product_id,
            ]);

            return null;
        }
    }

    /**
     * Archive (deactivate) a Stripe product.
     */
    public function archiveProduct(string $stripeProductId): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        $this->initStripe();

        try {
            \Stripe\Product::update($stripeProductId, [
                'active' => false,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe product archive failed', [
                'error' => $e->getMessage(),
                'stripe_product_id' => $stripeProductId,
            ]);
        }
    }

    /**
     * Create a Stripe price for a product.
     */
    public function createPrice(Product $product): ?Price
    {
        if (!$this->isConfigured() || !$product->stripe_product_id) {
            return null;
        }

        $this->initStripe();

        try {
            $priceData = [
                'product' => $product->stripe_product_id,
                'unit_amount' => (int) ($product->price * 100), // Convert to cents
                'currency' => strtolower($product->currency ?? 'usd'),
                'metadata' => [
                    'local_product_id' => $product->id,
                ],
            ];

            // Add recurring configuration if needed
            if ($product->isRecurring() && $product->recurring_interval) {
                $priceData['recurring'] = [
                    'interval' => $product->recurring_interval,
                ];
            }

            return Price::create($priceData);
        } catch (ApiErrorException $e) {
            Log::error('Stripe price creation failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
            ]);

            return null;
        }
    }

    /**
     * Archive (deactivate) a Stripe price.
     */
    public function archivePrice(string $stripePriceId): void
    {
        if (!$this->isConfigured()) {
            return;
        }

        $this->initStripe();

        try {
            Price::update($stripePriceId, [
                'active' => false,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe price archive failed', [
                'error' => $e->getMessage(),
                'stripe_price_id' => $stripePriceId,
            ]);
        }
    }

    /**
     * Handle checkout session completed event.
     */
    protected function handleCheckoutSessionCompleted(Session $session): void
    {
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'customer_email' => $session->customer_details->email ?? null,
            'amount_total' => $session->amount_total,
            'metadata' => $session->metadata?->toArray(),
        ]);
    }

    /**
     * Handle payment intent succeeded event.
     */
    protected function handlePaymentIntentSucceeded(\Stripe\PaymentIntent $paymentIntent): void
    {
        Log::info('Payment succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
        ]);
    }

    /**
     * Handle payment intent failed event.
     */
    protected function handlePaymentIntentFailed(\Stripe\PaymentIntent $paymentIntent): void
    {
        Log::warning('Payment failed', [
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error?->message,
        ]);
    }

    /**
     * Initialize Stripe with the secret key.
     */
    protected function initStripe(): void
    {
        Stripe::setApiKey($this->getSettings()->stripe_secret_key);
    }

    /**
     * Get site settings (cached).
     */
    protected function getSettings(): SiteSettings
    {
        if ($this->settings === null) {
            $this->settings = SiteSettings::current();
        }

        return $this->settings;
    }
}
