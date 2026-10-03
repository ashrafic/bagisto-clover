<?php

return [
    'description' => 'Pay securely with your credit/debit card via Clover.',
    'title' => 'Clover',
    'shipping' => 'Shipping',
    'tax' => 'Tax',
    'discount' => 'Discount',

    'response' => [
        'cart-not-found' => 'Cart not found or invalid.',
        'cart-processed' => 'This cart has already been processed.',
        'invalid-session' => 'Payment session is invalid.',
        'payment-cancelled' => 'Payment was cancelled.',
        'payment-failed' => 'Payment failed.',
        'payment-success' => 'Payment completed successfully.',
        'provide-credentials' => 'Please provide valid Clover credentials.',
        'session-invalid' => 'Payment session has expired or is invalid.',
        'session-not-found' => 'Payment session not found.',
        'verification-failed' => 'Payment verification failed.',
        'webhook-secret-missing' => 'Webhook signing secret is not configured.',
    ],

    'configuration' => [
        'api-token' => 'API Token',
        'api-test-token' => 'API Test Token',
        'clover' => 'Clover',
        'clover-info' => 'Accept payments through the Clover Hosted Checkout page. Generate an Ecommerce API token for the Hosted Checkout integration type from the Clover Merchant Dashboard, and register the store webhook URL (https://your-store.com/clover/webhook) on the Hosted Checkout settings page along with its signing secret.',
        'merchant-id' => 'Merchant ID',
        'page-config-uuid' => 'Page Config UUID',
        'test-merchant-id' => 'Test Merchant ID',
        'webhook-secret' => 'Webhook Signing Secret',
        'webhook-secret-information' => 'Enter the signing secret generated for the webhook URL configured on the Clover Merchant Dashboard Hosted Checkout settings page.',
    ],
];
