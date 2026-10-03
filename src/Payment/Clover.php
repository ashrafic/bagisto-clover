<?php

namespace Webkul\Clover\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Checkout\Facades\Cart;
use Webkul\Payment\Payment\Payment;

class Clover extends Payment
{
    /**
     * Sandbox API base URL.
     *
     * @var string
     */
    public const SANDBOX_API_URL = 'https://apisandbox.dev.clover.com';

    /**
     * Production API base URL.
     *
     * @var string
     */
    public const PRODUCTION_API_URL = 'https://api.clover.com';

    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'clover';

    /**
     * Get redirect URL for Clover payment.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('clover.standard.redirect');
    }

    /**
     * Check if payment method is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return parent::isAvailable() && $this->hasValidCredentials();
    }

    /**
     * Get payment method title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getConfigData('title') ?? trans('clover::app.title');
    }

    /**
     * Get payment method description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getConfigData('description') ?? trans('clover::app.description');
    }

    /**
     * Get payment method image.
     *
     * @return string
     */
    public function getImage()
    {
        if ($url = $this->getConfigData('image')) {
            return Storage::url($url);
        }

        return route('clover.logo');
    }

    /**
     * Get API base URL based on the configured environment.
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->getConfigData('sandbox')
            ? self::SANDBOX_API_URL
            : self::PRODUCTION_API_URL;
    }

    /**
     * Get Clover API key.
     *
     * @return string|null
     */
    public function getApiKey()
    {
        $isSandbox = $this->getConfigData('sandbox');

        return $isSandbox
            ? $this->getConfigData('api_test_key')
            : $this->getConfigData('api_key');
    }

    /**
     * Get Clover merchant identifier.
     *
     * @return string|null
     */
    public function getMerchantId()
    {
        $isSandbox = $this->getConfigData('sandbox');

        return $isSandbox
            ? $this->getConfigData('test_merchant_id')
            : $this->getConfigData('merchant_id');
    }

    /**
     * Get Clover hosted checkout webhook signing secret.
     *
     * @return string|null
     */
    public function getWebhookSecret()
    {
        $isSandbox = $this->getConfigData('sandbox');

        return $isSandbox
            ? $this->getConfigData('test_webhook_secret')
            : $this->getConfigData('webhook_secret');
    }

    /**
     * Check if required credentials are configured.
     *
     * @return bool
     */
    public function hasValidCredentials()
    {
        return $this->getApiKey() && $this->getMerchantId();
    }

    /**
     * Create a Clover hosted checkout session.
     *
     * @param  \Webkul\Checkout\Contracts\Cart|null  $cart
     * @return object|false
     */
    public function createCheckoutSession($cart = null)
    {
        if (! $cart) {
            $cart = Cart::getCart();
        }

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->withHeaders([
                    'X-Clover-Merchant-Id' => $this->getMerchantId(),
                ])
                ->withToken($this->getApiKey())
                ->post($this->getApiUrl().'/invoicingcheckoutservice/v1/checkouts', $this->preparePayload($cart));

            if (! $response->successful()) {
                return false;
            }

            $session = $response->json();

            if (empty($session['href']) || empty($session['checkoutSessionId'])) {
                return false;
            }

            return (object) $session;
        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Validate the Clover-Signature header of a hosted checkout webhook.
     *
     * @param  string|null  $signature
     * @param  string  $payload
     * @return bool
     */
    public function verifyWebhookSignature($signature, $payload)
    {
        $secret = $this->getWebhookSecret();

        if (empty($signature) || empty($secret)) {
            return false;
        }

        $timestamp = Str::before($signature, ',');

        if (! Str::startsWith($timestamp, 't=')) {
            return false;
        }

        $timestamp = substr($timestamp, 2);

        $providedSignature = Str::after($signature, 'v1=');

        $expectedSignature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return hash_equals($expectedSignature, $providedSignature);
    }

    /**
     * Prepare the hosted checkout session request payload.
     *
     * @param  \Webkul\Checkout\Contracts\Cart  $cart
     * @return array
     */
    private function preparePayload($cart)
    {
        $payload = [
            'customer' => $this->prepareCustomer($cart),
            'shoppingCart' => [
                'lineItems' => $this->prepareLineItems($cart),
            ],
            'redirectUrls' => [
                'success' => route('clover.payment.success').'?session_id={CHECKOUT_SESSION_ID}',
                'failure' => route('clover.payment.cancel'),
            ],
        ];

        if ($pageConfigUuid = $this->getConfigData('page_config_uuid')) {
            $payload['pageConfigUuid'] = $pageConfigUuid;
        }

        return $payload;
    }

    /**
     * Prepare customer information from the cart billing address.
     *
     * @param  \Webkul\Checkout\Contracts\Cart  $cart
     * @return array
     */
    private function prepareCustomer($cart)
    {
        $address = $cart->billing_address ?? $cart->shipping_address;

        $customer = [
            'firstName' => $address->first_name ?? $cart->customer_first_name,
            'lastName' => $address->last_name ?? $cart->customer_last_name,
            'email' => $address->email ?? $cart->customer_email,
        ];

        if (! empty($address->phone)) {
            $customer['phoneNumber'] = $address->phone;
        }

        return array_filter($customer);
    }

    /**
     * Convert cart items, shipping and tax totals into hosted checkout line items.
     *
     * @param  \Webkul\Checkout\Contracts\Cart  $cart
     * @return array
     */
    private function prepareLineItems($cart)
    {
        $lineItems = [];

        foreach ($cart->items as $item) {
            $lineItems[] = [
                'name' => $item->name,
                'note' => $item->sku,
                'price' => $this->formatAmount($item->base_price),
                'unitQty' => (int) $item->quantity,
            ];
        }

        if ($cart->base_shipping_amount > 0) {
            $lineItems[] = [
                'name' => trans('clover::app.shipping'),
                'price' => $this->formatAmount($cart->base_shipping_amount),
                'unitQty' => 1,
            ];
        }

        if ($cart->base_tax_total > 0) {
            $lineItems[] = [
                'name' => trans('clover::app.tax'),
                'price' => $this->formatAmount($cart->base_tax_total),
                'unitQty' => 1,
            ];
        }

        if ($cart->base_discount_amount > 0) {
            $lineItems[] = [
                'name' => trans('clover::app.discount'),
                'price' => -$this->formatAmount($cart->base_discount_amount),
                'unitQty' => 1,
            ];
        }

        return $lineItems;
    }

    /**
     * Convert an amount to the smallest currency unit expected by Clover.
     *
     * Clover expects amounts as an integer in the currency's smallest unit, which
     * is derived from the number of decimal digits configured for the base
     * currency. For zero-decimal currencies (such as JPY) the amount is used
     * as-is.
     *
     * @param  float  $amount
     * @return int
     */
    private function formatAmount($amount)
    {
        $decimal = core()->getBaseCurrency()->decimal ?? 2;

        return (int) round($amount * (10 ** $decimal));
    }
}
