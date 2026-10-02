<?php

return [
    'description' => 'Plătiți în siguranță cu cardul dvs. de credit/debit prin Clover.',
    'title' => 'Clover',
    'shipping' => 'Livrare',
    'tax' => 'Taxă',
    'discount' => 'Reducere',

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
        'webhook-secret-missing' => 'Cheia de semnăture a webhook-ului nu este configurată.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Acceptați plăți prin pagina Clover Hosted Checkout. Generați un token API de comerț electronic de tip Hosted Checkout din tabloul de bord al comerciantului Clover și înregistrați URL-ul webhook al magazinului (https://your-store.com/clover/webhook) pe pagina de setări Hosted Checkout împreună cu cheia de semnătură.',
        'merchant-id' => 'Merchant ID',
        'page-config-uuid' => 'UUID configurație pagină',
        'test-merchant-id' => 'ID comerciant de test',
        'webhook-secret' => 'Cheie de semnătură webhook',
        'webhook-secret-information' => 'Introduceți cheia de semnătură generată pentru URL-ul webhook configurat pe pagina de setări Hosted Checkout din tabloul de bord al comerciantului Clover.',
    ],
];
