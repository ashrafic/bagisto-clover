<?php

return [
    'description' => 'Безопасно платите кредитной или дебетовой картой через Clover.',
    'title' => 'Clover',
    'shipping' => 'Доставка',
    'tax' => 'Налог',
    'discount' => 'Скидка',

    'response' => [
        'cart-not-found' => 'Корзина не найдена или недействительна.',
        'cart-processed' => 'Эта корзина уже была обработана.',
        'invalid-session' => 'Сессия платежа недействительна.',
        'payment-cancelled' => 'Платеж был отменен.',
        'payment-failed' => 'Платеж не удался.',
        'payment-success' => 'Платеж успешно завершен.',
        'provide-credentials' => 'Пожалуйста, предоставьте действительные учетные данные Clover.',
        'session-invalid' => 'Сессия платежа истекла или недействительна.',
        'session-not-found' => 'Сессия платежа не найдена.',
        'verification-failed' => 'Проверка платежа не удалась.',
        'webhook-secret-missing' => 'Ключ подписи вебхука не настроен.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Принимайте платежи через страницу Clover Hosted Checkout. Сгенерируйте токен API электронной коммерции типа Hosted Checkout в панели продавца Clover и зарегистрируйте URL-адрес вебхука магазина (https://your-store.com/clover/webhook) на странице настроек Hosted Checkout вместе с ключом подписи.',
        'merchant-id' => 'ID продавца',
        'page-config-uuid' => 'UUID конфигурации страницы',
        'test-merchant-id' => 'Тестовый ID продавца',
        'webhook-secret' => 'Ключ подписи вебхука',
        'webhook-secret-information' => 'Введите ключ подписи, сгенерированный для URL-адреса вебхука, настроенного на странице настроек Hosted Checkout панели продавца Clover.',
    ],
];
