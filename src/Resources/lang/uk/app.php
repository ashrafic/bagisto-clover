<?php

return [
    'description' => 'Безпечно сплачуйте кредитною або дебетовою карткою через Clover.',
    'title' => 'Clover',
    'shipping' => 'Доставка',
    'tax' => 'Податок',
    'discount' => 'Знижка',

    'response' => [
        'cart-not-found' => 'Кошик не знайдено або недійсний.',
        'cart-processed' => 'Цей кошик вже оброблено.',
        'invalid-session' => 'Сесія платежу недійсна.',
        'payment-cancelled' => 'Платіж скасовано.',
        'payment-failed' => 'Платіж не вдався.',
        'payment-success' => 'Платіж успішно завершено.',
        'provide-credentials' => 'Будь ласка, надайте дійсні облікові дані Clover.',
        'session-invalid' => 'Термін дії сесії платежу закінчився або недійсна.',
        'session-not-found' => 'Сесія платежу не знайдена.',
        'verification-failed' => 'Перевірка платежу не вдалася.',
        'webhook-secret-missing' => 'Ключ підпису вебхука не налаштовано.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Приймайте платежі через сторінку Clover Hosted Checkout. Згенеруйте токен API електронної комерції типу Hosted Checkout на панелі продавця Clover і зареєструйте URL-адресу вебхука магазину (https://your-store.com/clover/webhook) на сторінці налаштувань Hosted Checkout разом із ключем підпису.',
        'merchant-id' => 'ID продавця',
        'page-config-uuid' => 'UUID конфігурації сторінки',
        'test-merchant-id' => 'Тестовий ID продавця',
        'webhook-secret' => 'Ключ підпису вебхука',
        'webhook-secret-information' => 'Введіть ключ підпису, згенерований для URL-адреси вебхука, налаштованої на сторінці налаштувань Hosted Checkout панелі продавця Clover.',
    ],
];
