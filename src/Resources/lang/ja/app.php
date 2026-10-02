<?php

return [
    'description' => 'Clover経由でクレジット/デビットカードで安全に支払う。',
    'title' => 'Clover',
    'shipping' => '配送',
    'tax' => '税金',
    'discount' => '割引',

    'response' => [
        'cart-not-found' => 'カートが見つからないか無効です。',
        'cart-processed' => 'このカートは既に処理されています。',
        'invalid-session' => '支払いセッションが無効です。',
        'payment-cancelled' => '支払いがキャンセルされました。',
        'payment-failed' => '支払いに失敗しました。',
        'payment-success' => '支払いが正常に完了しました。',
        'provide-credentials' => '有効な Clover 認証情報を提供してください。',
        'session-invalid' => '支払いセッションが期限切れまたは無効です。',
        'session-not-found' => '支払いセッションが見つかりません。',
        'verification-failed' => '支払い検証に失敗しました。',
        'webhook-secret-missing' => 'Webhook署名キーが設定されていません。',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Cloverホストチェックアウトページで支払いを受け付けます。CloverマーチャントダッシュボードでHosted CheckoutタイプのEコマースAPIトークンを生成し、ストアのWebhook URL（https://your-store.com/clover/webhook）を署名キーとともにHosted Checkout設定ページに登録してください。',
        'merchant-id' => 'マーチャントID',
        'page-config-uuid' => 'ページ設定UUID',
        'test-merchant-id' => 'テストマーチャントID',
        'webhook-secret' => 'Webhook署名キー',
        'webhook-secret-information' => 'CloverマーチャントダッシュボードのHosted Checkout設定ページで設定されたWebhook URLに対して生成された署名キーを入力してください。',
    ],
];
