<?php

return [
    'description' => 'পে সিকিউর উইথ ইউর ক্রেডিট/ডেবিট কার্ড ভায়া ক্লোভার।',
    'title' => 'Clover',
    'shipping' => 'শিপিং',
    'tax' => 'ট্যাক্স',
    'discount' => 'ডিসকাউন্ট',

    'response' => [
        'cart-not-found' => 'কার্ট পাওয়া যায়নি বা অবৈধ।',
        'cart-processed' => 'এই কার্টটি ইতিমধ্যে প্রসেস করা হয়েছে।',
        'invalid-session' => 'পেমেন্ট সেশন অবৈধ।',
        'payment-cancelled' => 'পেমেন্ট বাতিল করা হয়েছে।',
        'payment-failed' => 'পেমেন্ট ব্যর্থ।',
        'payment-success' => 'পেমেন্ট সফলভাবে সম্পন্ন হয়েছে।',
        'provide-credentials' => 'দয়া করে বৈধ Clover ক্রেডেনশিয়াল প্রদান করুন।',
        'session-invalid' => 'পেমেন্ট সেশনের মেয়াদ শেষ বা অবৈধ।',
        'session-not-found' => 'পেমেন্ট সেশন পাওয়া যায়নি।',
        'verification-failed' => 'পেমেন্ট যাচাইকরণ ব্যর্থ।',
        'webhook-secret-missing' => 'ওয়েবহুক সাইনিং সিক্রেট কনফিগার করা হয়নি।',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Clover হোস্টেড চেকআউট পেজের মাধ্যমে পেমেন্ট গ্রহণ করুন। Clover মার্চেন্ট ড্যাশবোর্ড থেকে Hosted Checkout ইন্টিগ্রেশন টাইপের জন্য একটি ই-কমার্স API টোকেন তৈরি করুন এবং স্টোরের ওয়েবহুক URL (https://your-store.com/clover/webhook) সাইনিং সিক্রেটসহ হোস্টেড চেকআউট সেটিংস পেজে নিবন্ধন করুন।',
        'merchant-id' => 'মার্চেন্ট আইডি',
        'page-config-uuid' => 'পেজ কনফিগ UUID',
        'test-merchant-id' => 'টেস্ট মার্চেন্ট আইডি',
        'webhook-secret' => 'ওয়েবহুক সাইনিং সিক্রেট',
        'webhook-secret-information' => 'Clover মার্চেন্ট ড্যাশবোর্ডের হোস্টেড চেকআউট সেটিংস পেজে কনফিগার করা ওয়েবহুক URL-এর জন্য তৈরি সাইনিং সিক্রেটটি লিখুন।',
    ],
];
