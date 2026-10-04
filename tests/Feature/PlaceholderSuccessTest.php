<?php

use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Models\CloverCheckoutSession as CloverCheckoutSessionModel;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;
use Webkul\Core\Models\CoreConfig;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderTransaction;

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

it('deactivates a still-active cart when recovering an order created by an earlier failed attempt', function () {
    // Arrange - the order was committed during the crashed attempt, cart stayed active
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_recovery_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_recovery_123']));

    $cart->refresh();

    expect($cart->is_active)->toBe(0);

    // Act - the customer retries the success return after a failure was fixed
    $cart->update(['is_active' => 1]);

    $session = CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_recovery_123')->first();

    CloverCheckoutSessionModel::where('id', $session->id)->update(['status' => CloverCheckoutSession::STATUS_NEW]);

    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_recovery_123']));

    // Assert - the order is reused and the cart is cleared again
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $cart->refresh();

    expect($cart->is_active)->toBe(0)
        ->and(Order::where('cart_id', $cart->id)->count())->toBe(1);
});

it('settles abandoned paid checkout sessions whose customer never returned', function () {
    // Arrange - paid session, browser never came back
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_abandoned_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_PAID,
        'payment_id' => 'clover_payment_abandoned_uuid',
        'verified_via' => CloverCheckoutSession::VERIFIED_VIA_WEBHOOK,
    ]);

    CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_abandoned_123')
        ->update(['updated_at' => now()->subHours(2)]);

    // Act
    $this->artisan('clover:settle-abandoned', ['--minutes' => 60]);

    // Assert
    $order = Order::where('cart_id', $cart->id)->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('processing')
        ->and($order->payment->additional['clover_payment_id'])->toBe('clover_payment_abandoned_uuid');

    expect(OrderTransaction::where('transaction_id', 'clover_cs_abandoned_123')->first())->not->toBeNull();

    $cart->refresh();

    expect($cart->is_active)->toBe(0);

    expect(CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_abandoned_123')->value('status'))
        ->toBe(CloverCheckoutSession::STATUS_PROCESSED);
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

it('never creates a duplicate order when a partial failure left the session pending', function () {
    // Arrange - the browser created the order but a failure left the session unprocessed
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_partial_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_partial_123']));

    CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_partial_123')
        ->update(['status' => CloverCheckoutSession::STATUS_NEW]);

    // Act - the customer's browser hits the success return again
    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_partial_123']));

    // Assert - the existing order is reused, nothing duplicated
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);

    expect(CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_partial_123')->value('status'))
        ->toBe(CloverCheckoutSession::STATUS_PROCESSED);
});
