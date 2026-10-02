<?php

return [
    'description' => 'Bayar dengan aman menggunakan kartu kredit/debit Anda melalui Clover.',
    'title' => 'Clover',
    'shipping' => 'Pengiriman',
    'tax' => 'Pajak',
    'discount' => 'Diskon',

    'response' => [
        'cart-not-found' => 'Keranjang tidak ditemukan atau tidak valid.',
        'cart-processed' => 'Keranjang ini telah diproses.',
        'invalid-session' => 'Sesi pembayaran tidak valid.',
        'payment-cancelled' => 'Pembayaran dibatalkan.',
        'payment-failed' => 'Pembayaran gagal.',
        'payment-success' => 'Pembayaran berhasil diselesaikan.',
        'provide-credentials' => 'Harap berikan kredensial Clover yang valid.',
        'session-invalid' => 'Sesi pembayaran kedaluwarsa atau tidak valid.',
        'session-not-found' => 'Sesi pembayaran tidak ditemukan.',
        'verification-failed' => 'Verifikasi pembayaran gagal.',
        'webhook-secret-missing' => 'Kunci tanda tangan webhook belum dikonfigurasi.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Terima pembayaran melalui halaman Clover Hosted Checkout. Buat token API e-commerce dengan tipe Hosted Checkout dari Dasbor Pedagang Clover dan daftarkan URL webhook toko (https://your-store.com/clover/webhook) di halaman pengaturan Hosted Checkout bersama kunci tanda tangannya.',
        'merchant-id' => 'ID Merchant',
        'page-config-uuid' => 'UUID Konfigurasi Halaman',
        'test-merchant-id' => 'ID Pedagang Pengujian',
        'webhook-secret' => 'Kunci Tanda Tangan Webhook',
        'webhook-secret-information' => 'Masukkan kunci tanda tangan yang dibuat untuk URL webhook yang dikonfigurasi pada halaman pengaturan Hosted Checkout Dasbor Pedagang Clover.',
    ],
];
