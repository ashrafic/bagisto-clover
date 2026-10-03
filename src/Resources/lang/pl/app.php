<?php

return [
    'description' => 'Płać bezpiecznie kartą kredytową/debetową przez Clover.',
    'title' => 'Clover',
    'shipping' => 'Wysyłka',
    'tax' => 'Podatek',
    'discount' => 'Rabat',

    'response' => [
        'cart-not-found' => 'Koszyk nie został znaleziony lub jest nieprawidłowy.',
        'cart-processed' => 'Ten koszyk został już przetworzony.',
        'invalid-session' => 'Sesja płatności jest nieprawidłowa.',
        'payment-cancelled' => 'Płatność została anulowana.',
        'payment-failed' => 'Płatność nie powiodła się.',
        'payment-success' => 'Płatność została pomyślnie zakończona.',
        'provide-credentials' => 'Proszę podać prawidłowe dane uwierzytelniające Clover.',
        'session-invalid' => 'Sesja płatności wygasła lub jest nieprawidłowa.',
        'session-not-found' => 'Nie znaleziono sesji płatności.',
        'verification-failed' => 'Weryfikacja płatności nie powiodła się.',
        'webhook-secret-missing' => 'Klucz podpisu webhooka nie jest skonfigurowany.',
    ],

    'configuration' => [
        'api-token' => 'Token API',
        'api-test-token' => 'Token API (testowy)',
        'clover' => 'Clover',
        'clover-info' => 'Akceptuj płatności przez stronę Clover Hosted Checkout. Wygeneruj token API e-commerce typu Hosted Checkout w panelu sprzedawcy Clover i zarejestruj adres URL webhooka sklepu (https://your-store.com/clover/webhook) na stronie ustawień Hosted Checkout wraz z kluczem podpisu.',
        'merchant-id' => 'ID sprzedawcy',
        'page-config-uuid' => 'UUID konfiguracji strony',
        'test-merchant-id' => 'Testowy identyfikator sprzedawcy',
        'webhook-secret' => 'Klucz podpisu webhooka',
        'webhook-secret-information' => 'Wprowadź klucz podpisu wygenerowany dla adresu URL webhooka skonfigurowanego na stronie ustawień Hosted Checkout w panelu sprzedawcy Clover.',
    ],
];
