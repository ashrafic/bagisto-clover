<?php

return [
    'description' => 'שלמו בבטחה בכרטיס האשראי/דייט שלכם דרך Clover.',
    'title' => 'Clover',
    'shipping' => 'משלוח',
    'tax' => 'מס',
    'discount' => 'הנחה',

    'response' => [
        'cart-not-found' => 'עגלת הקניות לא נמצאה  לא תקפה.',
        'cart-processed' => 'עג הקניות הזו כבר עובדה.',
        'invalid-session' => 'הפעלת התשלום לא תקפה.',
        'payment-cancelled' => 'התשלום בוטל.',
        'payment-failed' => 'התשלום נכשל.',
        'payment-success' => 'התשלום הושלם בהצלחה.',
        'provide-credentials' => 'אנא ספקו פרטי אימות תקפים  Clover.',
        'session-invalid' => 'הפע התשלום פגה או לא תקפה.',
        'session-not-found' => 'הפעלת התשלום לא נמצאה.',
        'verification-failed' => 'אימות התשלום נכשל.',
        'webhook-secret-missing' => 'מפתח החתימה של הוובהוק אינו מוגדר.',
    ],

    'configuration' => [
        'api-token' => 'אסימון API',
        'api-test-token' => 'אסימון API לבדיקה',
        'clover' => 'Clover',
        'clover-info' => 'קבלו תשלומים דרך עמוד Clover Hosted Checkout. צרו טוקן API למסחר אלקטרוני מסוג Hosted Checkout בלוח הבקרה של הסוחר ב-Clover ורשמו את כתובת ה-URL של הוובהוק של החנות (https://your-store.com/clover/webhook) בעמוד ההגדרות של Hosted Checkout יחד עם מפתח החתימה.',
        'merchant-id' => 'מזהה סוחר',
        'page-config-uuid' => 'UUID של תצורת עמוד',
        'test-merchant-id' => 'מזהה סוחר לבדיקה',
        'webhook-secret' => 'מפתח חתימת וובהוק',
        'webhook-secret-information' => 'הזינו את מפתח החתימה שנוצר עבור כתובת ה-URL של הוובהוק שהוגדרה בעמוד ההגדרות של Hosted Checkout בלוח הבקרה של הסוחר ב-Clover.',
    ],
];
