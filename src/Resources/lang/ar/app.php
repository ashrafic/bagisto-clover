<?php

return [
    'description' => 'ادفع بأمان عبر بطاقتك الائتمانية أو بطاقة الخصم من خلال Clover.',
    'title' => 'Clover',
    'shipping' => 'الشحن',
    'tax' => 'الضريبة',
    'discount' => 'خصم',

    'response' => [
        'cart-not-found' => 'السلة غير موجودة أو غير صالحة.',
        'cart-processed' => 'تمت معالجة هذه السلة بالفعل.',
        'invalid-session' => 'جلسة الدفع غير صالحة.',
        'payment-cancelled' => 'تم إلغاء الدفع.',
        'payment-failed' => 'فشل الدفع.',
        'payment-success' => 'تم الدفع بنجاح.',
        'provide-credentials' => 'يرجى تقديم بيانات اعتماد Clover صالحة.',
        'session-invalid' => 'انتهت صلاحية جلسة الدفع أو غير صالحة.',
        'session-not-found' => 'لم يتم العثور على جلسة الدفع.',
        'verification-failed' => 'فشل التحقق من الدفع.',
        'webhook-secret-missing' => 'لم يتم تكوين مفتاح توقيع الويب هوك.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'اقبل المدفوعات عبر صفحة Clover Hosted Checkout. أنشئ رمز واجهة برمجة التطبيقات للتجارة الإلكترونية من نوع Hosted Checkout من لوحة تحكم تاجر Clover وسجّل عنوان URL الخاص بالويب هوك للمتجر (https://your-store.com/clover/webhook) في صفحة إعدادات Hosted Checkout مع مفتاح التوقيع.',
        'merchant-id' => 'معرّف التاجر',
        'page-config-uuid' => 'UUID لتكوين الصفحة',
        'test-merchant-id' => 'معرّف التاجر التجريبي',
        'webhook-secret' => 'مفتاح توقيع الويب هوك',
        'webhook-secret-information' => 'أدخل مفتاح التوقيع الذي تم إنشاؤه لعنوان URL الخاص بالويب هوك والمكون في صفحة إعدادات Hosted Checkout في لوحة تحكم تاجر Clover.',
    ],
];
