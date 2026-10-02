<?php

return [
    'description' => 'با کارت اعتباری/بدهی خود از طریق Clover با خیال راحت پرداخت کنید.',
    'title' => 'Clover',
    'shipping' => 'حمل و نقل',
    'tax' => 'مالیات',
    'discount' => 'تخفیف',

    'response' => [
        'cart-not-found' => 'سبد خرید یافت نشد یا نامعتبر است.',
        'cart-processed' => 'این سبد خرید قبلاً پردازش شده است.',
        'invalid-session' => 'جلسه پرداخت نامعتبر است.',
        'payment-cancelled' => 'پرداخت لغو شد.',
        'payment-failed' => 'پرداخت ناموفق.',
        'payment-success' => 'پرداخت با موفقیت تکمیل شد.',
        'provide-credentials' => 'لطفاً اعتبارنامه‌های معتبر Clover ارائه دهید.',
        'session-invalid' => 'جلسه پرداخت منقضی شده یا نامعتبر است.',
        'session-not-found' => 'جلسه پرداخت یافت نشد.',
        'verification-failed' => 'تأیید پرداخت ناموفق بود.',
        'webhook-secret-missing' => 'کلید امضای وب‌هوک پیکربندی نشده است.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'پرداخت‌ها را از طریق صفحه Clover Hosted Checkout بپذیرید. یک توکن API تجارت الکترونیک از نوع Hosted Checkout در داشبورد پذیرنده Clover ایجاد کنید و URL وب‌هوک فروشگاه (https://your-store.com/clover/webhook) را همراه با کلید امضا در صفحه تنظیمات Hosted Checkout ثبت کنید.',
        'merchant-id' => 'شناسه فروشنده',
        'page-config-uuid' => 'UUID پیکربندی صفحه',
        'test-merchant-id' => 'شناسه پذیرنده آزمایشی',
        'webhook-secret' => 'کلید امضای وب‌هوک',
        'webhook-secret-information' => 'کلید امضای ایجاد شده برای URL وب‌هوک پیکربندی شده در صفحه تنظیمات Hosted Checkout داشبورد پذیرنده Clover را وارد کنید.',
    ],
];
