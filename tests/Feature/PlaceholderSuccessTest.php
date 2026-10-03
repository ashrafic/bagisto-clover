<?php

use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Models\CloverCheckoutSession as CloverCheckoutSessionModel;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;
use Webkul\Core\Models\CoreConfig;
use Webkul\Sales\Models\Order;

beforeEach(function () {
    config(['services.clover.webhook_wait' => 0]);

    $this->cloverConfigBackup = CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->get()->toArray();

    CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->delete();

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.active',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_webhook_secret',
        'value' => 'wh_secret_test',
        'channel_code' => 'default',
    ]);
});

afterEach(function () {
    CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->delete();

    if (! empty($this->cloverConfigBackup)) {
        CoreConfig::insert($this->cloverConfigBackup);
    }
});

it('resolves the checkout session via the active cart when the session id placeholder is not interpolated', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_placeholder_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    // Act - Clover redirected back with the literal, un-interpolated placeholder
    $response = $this->get(route('clover.payment.success').'?session_id='.'{CHECKOUT_SESSION_ID}');

    // Assert
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('success');

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);
});

it('never creates a duplicate order when a partial webhook failure left the session pending', function () {
    // Arrange - webhook processed the payment but crashed before marking the session
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_partial_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $this->postSignedWebhook([
        'id' => 'clover_payment_partial_uuid',
        'status' => 'APPROVED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_partial_123',
    ]);

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);

    CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_partial_123')
        ->update(['status' => CloverCheckoutSession::STATUS_NEW]);

    // Act - the customer's browser now returns from Clover
    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_partial_123']));

    // Assert - the existing order is reused, nothing duplicated
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);

    expect(CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_partial_123')->value('status'))
        ->toBe(CloverCheckoutSession::STATUS_PROCESSED);
});
