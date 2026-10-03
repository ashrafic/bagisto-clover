<?php

return [
    'description' => 'Betaal veilig met uw creditcard/debetkaart via Clover.',
    'title' => 'Clover',
    'shipping' => 'Verzending',
    'tax' => 'Belasting',
    'discount' => 'Korting',

    'response' => [
        'cart-not-found' => 'Winkelwagen niet gevonden of ongeldig.',
        'cart-processed' => 'Deze winkelwagen is al verwerkt.',
        'invalid-session' => 'Betalingssessie is ongeldig.',
        'payment-cancelled' => 'Betaling werd geannuleerd.',
        'payment-failed' => 'Betaling mislukt.',
        'payment-success' => 'Betaling succesvol voltooid.',
        'provide-credentials' => 'Gelieve geldige Clover-inloggegevens op te geven.',
        'session-invalid' => 'Betalingssessie is verlopen of ongeldig.',
        'session-not-found' => 'Betalingssessie niet gevonden.',
        'verification-failed' => 'Betalingsverificatie mislukt.',
        'webhook-secret-missing' => 'De handtekeningsleutel van de webhook is niet geconfigureerd.',
    ],

    'configuration' => [
        'api-token' => 'API-token',
        'api-test-token' => 'API-testtoken',
        'clover' => 'Clover',
        'clover-info' => 'Accepteer betalingen via de Clover Hosted Checkout-pagina. Genereer een e-commerce API-token van het type Hosted Checkout in het Clover Merchant Dashboard en registreer de webhook-URL van de winkel (https://your-store.com/clover/webhook) op de Hosted Checkout-instellingenpagina samen met de handtekeningsleutel.',
        'merchant-id' => 'Handelaar ID',
        'page-config-uuid' => 'Paginaconfiguratie-UUID',
        'test-merchant-id' => 'Test-Merchant-ID',
        'webhook-secret' => 'Webhook-handtekeningsleutel',
        'webhook-secret-information' => 'Voer de handtekeningsleutel in die is gegenereerd voor de webhook-URL die is geconfigureerd op de Hosted Checkout-instellingenpagina van het Clover Merchant Dashboard.',
    ],
];
