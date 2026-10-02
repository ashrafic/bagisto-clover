<?php

namespace Webkul\Clover\Tests;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Webkul\Payment\Tests\Concerns\ProvidePaymentHelpers;

class CloverTestCase extends TestCase
{
    use ProvidePaymentHelpers;

    /**
     * Post a Clover hosted checkout webhook payload with a valid signature.
     *
     * @return TestResponse
     */
    public function postSignedWebhook(array $payload)
    {
        $body = json_encode($payload);

        $timestamp = (string) time();

        $signature = 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$body, 'wh_secret_test');

        return $this->postJson(route('clover.payment.webhook'), $payload, [
            'Clover-Signature' => $signature,
        ]);
    }
}
