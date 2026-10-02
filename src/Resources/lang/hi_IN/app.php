<?php

return [
    'description' => 'Clover के माध्यम से अपने क्रेडिट/डेबिट कार्ड से सुरक्षित रूप से भुगतान करें।',
    'title' => 'Clover',
    'shipping' => 'शिपिंग',
    'tax' => 'कर',
    'discount' => 'छूट',

    'response' => [
        'cart-not-found' => 'कार्ट नहीं मिली या अमान्य है।',
        'cart-processed' => 'यह कार्ट पहले से प्रोसेस की गई है।',
        'invalid-session' => 'भुगतान सेशन अमान्य है।',
        'payment-cancelled' => 'भुगतान रद्द कर दिया गया।',
        'payment-failed' => 'भुगतान असफल।',
        'payment-success' => 'भुगतान सफलतापूर्वक पूरा हुआ।',
        'provide-credentials' => 'कृपया मान्य Clover क्रेडेंशियल प्रदान करें।',
        'session-invalid' => 'भुगतान सेशन समाप्त हो गया या अमान्य है।',
        'session-not-found' => 'भुगतान सेशन नहीं मिला।',
        'verification-failed' => 'भुगतान सत्यापन असफल।',
        'webhook-secret-missing' => 'वेबहुक साइनिंग सीक्रेट कॉन्फ़िगर नहीं है।',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Clover होस्टेड चेकआउट पेज के माध्यम से भुगतान स्वीकारें। Clover मर्चेंट डैशबोर्ड से Hosted Checkout इंटीग्रेशन प्रकार के लिए एक ई-कॉमर्स API टोकन जनरेट करें और स्टोर की वेबहुक URL (https://your-store.com/clover/webhook) को साइनिंग सीक्रेट के साथ होस्टेड चेकआउट सेटिंग्स पेज पर रजिस्टर करें।',
        'merchant-id' => 'मर्चेंट आईडी',
        'page-config-uuid' => 'पेज कॉन्फ़िग UUID',
        'test-merchant-id' => 'टेस्ट मर्चेंट आईडी',
        'webhook-secret' => 'वेबहुक साइनिंग सीक्रेट',
        'webhook-secret-information' => 'Clover मर्चेंट डैशबोर्ड के होस्टेड चेकआउट सेटिंग्स पेज पर कॉन्फ़िगर वेबहुक URL के लिए जनरेट की गई साइनिंग सीक्रेट दर्ज करें।',
    ],
];
