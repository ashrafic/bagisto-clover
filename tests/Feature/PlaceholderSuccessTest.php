<?php

use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;
use Webkul\Core\Models\CoreConfig;
use Webkul\Sales\Models\Order;

it('resolves the checkout session via the active cart when the session id placeholder is not interpolated', function () {
    // Arrange
    $cart = $this->createCartWithItems('clover');

    app(CloverCheckoutSessionRepository::class)->create([
        'cart_id'             => $cart->id,
        'checkout_session_id' => 'clover_cs_placeholder_123',
        'base_grand_total'    => $cart->base_grand_total,
        'currency_code'       => 'USD',
        'status'              => CloverCheckoutSession::STATUS_NEW,
    ]);

    // Act - Clover redirected back with the literal, un-interpolated placeholder
    $response = $this->get(route('clover.payment.success').'?session_id='.'{CHECKOUT_SESSION_ID}');

    // Assert
    $response->assertRedirect(route('shop.checkout.onepage.success'));

    $response->assertSessionHas('success');

    expect(Order::where('cart_id', $cart->id)->count())->toBe(1);
});
