<?php

return [
    'description' => '通过 Clover 使用您的信用卡/借记卡安全付款。',
    'title' => 'Clover',
    'shipping' => '配送',
    'tax' => '税费',
    'discount' => '折扣',

    'response' => [
        'cart-not-found' => '购物车未找到或无效。',
        'cart-processed' => '此购物车已被处理。',
        'invalid-session' => '付款会话无效。',
        'payment-cancelled' => '付款已取消。',
        'payment-failed' => '付款失败。',
        'payment-success' => '付款成功完成。',
        'provide-credentials' => '请提供有效的 Clover 凭据。',
        'session-invalid' => '付款会话已过期或无效。',
        'session-not-found' => '未找到付款会话。',
        'verification-failed' => '付款验证失败。',
        'webhook-secret-missing' => 'Webhook 签名密钥未配置。',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => '通过 Clover 托管结账页面接受付款。在 Clover 商家控制面板中生成托管结账类型的电子商务 API 令牌，并在托管结账设置页面上注册商店的 Webhook URL（https://your-store.com/clover/webhook）及其签名密钥。',
        'merchant-id' => '商户ID',
        'page-config-uuid' => '页面配置 UUID',
        'test-merchant-id' => '测试商家 ID',
        'webhook-secret' => 'Webhook 签名密钥',
        'webhook-secret-information' => '输入为在 Clover 商家控制面板的托管结账设置页面上配置的 Webhook URL 生成的签名密钥。',
    ],
];
