<?php

return [
    'description' => 'Sicher bezahlen mit Ihrer Kredit-/Debitkarte über Clover.',
    'title' => 'Clover',
    'shipping' => 'Versand',
    'tax' => 'Steuer',
    'discount' => 'Rabatt',

    'response' => [
        'cart-not-found' => 'Warenkorb nicht gefunden oder ungültig.',
        'cart-processed' => 'Dieser Warenkorb wurde bereits verarbeitet.',
        'invalid-session' => 'Zahlungssitzung ist ungültig.',
        'payment-cancelled' => 'Zahlung wurde abgebrochen.',
        'payment-failed' => 'Zahlung fehlgeschlagen.',
        'payment-success' => 'Zahlung erfolgreich abgeschlossen.',
        'provide-credentials' => 'Bitte geben Sie gültige Clover-Anmeldedaten an.',
        'session-invalid' => 'Zahlungssitzung ist abgelaufen oder ungültig.',
        'session-not-found' => 'Zahlungssitzung nicht gefunden.',
        'verification-failed' => 'Zahlungsverifizierung fehlgeschlagen.',
        'webhook-secret-missing' => 'Der Webhook-Signaturschlüssel ist nicht konfiguriert.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Zahlungen über die Clover-Hosted-Checkout-Seite akzeptieren. Erstellen Sie ein Ecommerce-API-Token vom Typ Hosted Checkout im Clover-Händler-Dashboard und registrieren Sie die Webhook-URL Ihres Shops (https://your-store.com/clover/webhook) auf der Hosted-Checkout-Einstellungsseite zusammen mit dem Signaturschlüssel.',
        'merchant-id' => 'Händler-ID',
        'page-config-uuid' => 'Seitenkonfigurations-UUID',
        'test-merchant-id' => 'Test-Händler-ID',
        'webhook-secret' => 'Webhook-Signaturschlüssel',
        'webhook-secret-information' => 'Geben Sie den Signaturschlüssel ein, der für die auf der Hosted-Checkout-Einstellungsseite des Clover-Händler-Dashboards konfigurierte Webhook-URL generiert wurde.',
    ],
];
