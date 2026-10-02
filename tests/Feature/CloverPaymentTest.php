<?php

use Webkul\Checkout\Facades\Cart;
use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Models\CloverCheckoutSession as CloverCheckoutSessionModel;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;
use Webkul\Core\Models\CoreConfig;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderTransaction;

beforeEach(function () {
    config(['services.clover.webhook_wait' => 0]);

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
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => 'private_test_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_merchant_id',
        'value' => 'TEST_MID',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_webhook_secret',
        'value' => 'wh_secret_test',
        'channel_code' => 'default',
    ]);
});

it('redirects to cart when clover credentials are invalid', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => '',
        'channel_code' => 'default',
    ]);

    // Act
    $response = $this->get(route('clover.standard.redirect'));

    // Assert
    $response->assertRedirect(route('shop.checkout.cart.index'));

    $response->assertSessionHas('error');
});

it('redirects to cart when cart is not found', function () {
    // Arrange
    Cart::shouldReceive('getCart')->andReturn(null);

    // Act
    $response = $this->get(route('clover.standard.redirect'));

    // Assert
    $response->assertRedirect(route('shop.checkout.cart.index'));

    $response->assertSessionHas('error');
});

it('redirects to cart when no checkout session can be resolved on success callback', function () {
    // Act
    $response = $this->get(route('clover.payment.success'));

    // Assert
    $response->assertRedirect(route('shop.checkout.cart.index'));

    $response->assertSessionHas('error');
});

it('shows error message on payment cancellation', function () {
    // Act
    $response = $this->get(route('clover.payment.cancel'));

    // Assert
    $response->assertRedirect(route('shop.checkout.cart.index'));

    $response->assertSessionHas('error');
});

it('successfully processes clover payment from the success redirect and creates order with invoice', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    $checkoutSession = app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_success_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    // Act
    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_success_123']));

    // Assert
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('success');

    $response->assertSessionHas('order_id');

    $order = Order::where('customer_id', $cart->customer_id)->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('processing')
        ->and($order->payment->method)->toBe('clover')
        ->and($order->payment->additional['clover_checkout_session_id'])->toBe('clover_cs_success_123')
        ->and($order->payment->additional['clover_verified_via'])->toBe(CloverCheckoutSession::VERIFIED_VIA_REDIRECT);

    $orderTransaction = OrderTransaction::where('transaction_id', 'clover_cs_success_123')->first();

    expect($orderTransaction)->not->toBeNull()
        ->and($orderTransaction->order_id)->toBe($order->id)
        ->and($orderTransaction->status)->toBe('APPROVED');

    $invoice = Invoice::where('order_id', $order->id)->first();

    expect($invoice)->not->toBeNull();

    expect(CloverCheckoutSessionModel::find($checkoutSession->id)->status)->toBe(CloverCheckoutSession::STATUS_PROCESSED);

    $cart->refresh();

    expect($cart->is_active)->toBe(0);
});

it('successfully processes clover payment from the webhook before the redirect returns', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_webhook_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $response = $this->postSignedWebhook([
        'id' => 'clover_payment_uuid_123',
        'status' => 'APPROVED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_webhook_123',
    ]);

    // Assert
    $response->assertOk();

    $order = Order::where('customer_id', $cart->customer_id)->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('processing')
        ->and($order->payment->additional['clover_payment_id'])->toBe('clover_payment_uuid_123');

    $orderTransaction = OrderTransaction::where('transaction_id', 'clover_cs_webhook_123')->first();

    expect($orderTransaction)->not->toBeNull()
        ->and($orderTransaction->order_id)->toBe($order->id);

    $cart->refresh();

    expect($cart->is_active)->toBe(0);

    $session = CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_webhook_123')->first();

    expect($session->status)->toBe(CloverCheckoutSession::STATUS_PROCESSED)
        ->and($session->verified_via)->toBe(CloverCheckoutSession::VERIFIED_VIA_WEBHOOK);
});

it('redirects the customer to the order success page when the webhook processed the payment first', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_dual_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $this->postSignedWebhook([
        'id' => 'clover_payment_uuid_dual',
        'status' => 'APPROVED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_dual_123',
    ]);

    // Act
    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_dual_123']));

    // Assert
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('success');

    $response->assertSessionHas('order_id');

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);
});

it('rejects a webhook with an invalid signature', function () {
    // Act
    $response = $this->postJson(route('clover.payment.webhook'), [
        'status' => 'APPROVED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_invalid_sig',
    ], ['Clover-Signature' => 't=1,v1=invalid']);

    // Assert
    $response->assertStatus(401);

    expect(OrderTransaction::where('transaction_id', 'clover_cs_invalid_sig')->count())->toBe(0)
        ->and(CloverCheckoutSessionModel::count())->toBe(0);
});

it('rejects a webhook when no signing secret is configured', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_webhook_secret',
        'value' => '',
        'channel_code' => 'default',
    ]);

    // Act
    $response = $this->postJson(route('clover.payment.webhook'), [
        'status' => 'APPROVED',
    ], ['Clover-Signature' => 't=1,v1=abc']);

    // Assert
    $response->assertStatus(401);
});

it('marks the checkout session as failed when the webhook reports a declined payment', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_declined_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    // Act
    $response = $this->postSignedWebhook([
        'status' => 'DECLINED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_declined_123',
    ]);

    // Assert
    $response->assertOk();

    expect(CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_declined_123')->first()->status)
        ->toBe(CloverCheckoutSession::STATUS_FAILED)
        ->and(Order::where('cart_id', $cart->id)->count())->toBe(0);
});
