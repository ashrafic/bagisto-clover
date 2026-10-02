<?php

return [
    'description' => 'Clover üzerinden kredi/banka kartınızla güvenle ödeme yapın.',
    'title' => 'Clover',
    'shipping' => 'Kargo',
    'tax' => 'Vergi',
    'discount' => 'İndirim',

    'response' => [
        'cart-not-found' => 'Sepet bulunamadı veya geçersiz.',
        'cart-processed' => 'Bu sepet zaten işlenmiş.',
        'invalid-session' => 'Ödeme oturumu geçersiz.',
        'payment-cancelled' => 'Ödeme iptal edildi.',
        'payment-failed' => 'Ödeme başarısız.',
        'payment-success' => 'Ödeme başarıyla tamamlandı.',
        'provide-credentials' => 'Lütfen geçerli Clover kimlik bilgileri sağlayın.',
        'session-invalid' => 'Ödeme oturumu süresi dolmuş veya geçersiz.',
        'session-not-found' => 'Ödeme oturumu bulunamadı.',
        'verification-failed' => 'Ödeme doğrulaması başarısız.',
        'webhook-secret-missing' => 'Webhook imza anahtarı yapılandırılmamış.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Clover Hosted Checkout sayfası aracılığıyla ödeme kabul edin. Clover Merchant Dashboard üzerinden Hosted Checkout türünde bir E-ticaret API anahtarı oluşturun ve mağazanın webhook URL\\\'sini (https://your-store.com/clover/webhook) imza anahtarıyla birlikte Hosted Checkout ayarlar sayfasına kaydedin.',
        'merchant-id' => 'Satıcı Kimliği',
        'page-config-uuid' => 'Sayfa Yapılandırma UUID\\\'si',
        'test-merchant-id' => 'Test Mağaza Kimliği',
        'webhook-secret' => 'Webhook İmza Anahtarı',
        'webhook-secret-information' => 'Clover Merchant Dashboard\\\'taki Hosted Checkout ayarlar sayfasında yapılandırılan webhook URL\\\'si için oluşturulan imza anahtarını girin.',
    ],
];
