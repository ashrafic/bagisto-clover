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

afterEach(function () {
    CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->delete();

    if (! empty($this->cloverConfigBackup)) {
        CoreConfig::insert($this->cloverConfigBackup);
    }
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

it('creates and settles the order from the webhook when it arrives first', function () {
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

    // Assert - the webhook settles the payment end to end
    $response->assertOk();

    $order = Order::where('cart_id', $cart->id)->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('processing')
        ->and($order->payment->additional['clover_payment_id'])->toBe('clover_payment_uuid_123')
        ->and($order->payment->additional['clover_verified_via'])->toBe(CloverCheckoutSession::VERIFIED_VIA_WEBHOOK);

    expect(OrderTransaction::where('transaction_id', 'clover_cs_webhook_123')->first())->not->toBeNull();

    $cart->refresh();

    expect($cart->is_active)->toBe(0);

    $session = CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_webhook_123')->first();

    expect($session->status)->toBe(CloverCheckoutSession::STATUS_PROCESSED)
        ->and($session->verified_via)->toBe(CloverCheckoutSession::VERIFIED_VIA_WEBHOOK);
});

it('shows the success page and clears the cart session when the browser returns after the webhook settled the order', function () {
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

    // Act - the customer's browser returns from Clover
    $response = $this->get(route('clover.payment.success', ['session_id' => 'clover_cs_dual_123']));

    // Assert - the webhook order is reused, the cart session is cleared, no duplicate
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('success');

    $response->assertSessionHas('order_id');

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1)
        ->and(session()->has('cart'))->toBeFalse();
});

it('resolves the payment through the guest session binding even when the webhook already deactivated the cart', function () {
    // Arrange - a guest cart whose session binding survives, the literal
    // placeholder was not interpolated and the webhook won the race
    $cart = $this->createCartWithItems('clover', [
        'is_guest' => 1,
        'customer_id' => null,
    ]);

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id' => $cart->id,
        'checkout_session_id' => 'clover_cs_session_binding_123',
        'base_grand_total' => $cart->base_grand_total,
        'currency_code' => 'USD',
        'status' => CloverCheckoutSession::STATUS_NEW,
    ]);

    $this->postSignedWebhook([
        'id' => 'clover_payment_uuid_binding',
        'status' => 'APPROVED',
        'type' => 'PAYMENT',
        'data' => 'clover_cs_session_binding_123',
    ]);

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);

    // the customer's browser session is untouched by the webhook's own request
    session()->put('cart', (object) ['id' => $cart->id]);

    // Act - browser returns with the un-interpolated placeholder
    $response = $this->get(route('clover.payment.success').'?session_id='.'{CHECKOUT_SESSION_ID}');

    // Assert - resolved via the session cart binding, order reused, success page
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('order_id');

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1)
        ->and(session()->has('cart'))->toBeFalse();
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
        ->and(CloverCheckoutSessionModel::where('checkout_session_id', 'clover_cs_invalid_sig')->count())->toBe(0);
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
