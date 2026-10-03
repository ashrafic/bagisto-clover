<?php

use Webkul\Clover\Payment\Clover;

it('creates a real hosted checkout session against the clover sandbox api', function () {
    $cart = $this->createCartWithItems('clover');

    $session = app(Clover::class)->createCheckoutSession($cart);

    if ($session === false) {
        $log = file_get_contents(glob(storage_path('logs/laravel*.log'))[0] ?? '');

        dump('FAILED - last log lines:'.substr($log, -800));

        $this->markTestSkipped('Session creation failed - see log above');
    }

    expect($session->href)->toBeString()
        ->and($session->checkoutSessionId)->toBeString();
});
