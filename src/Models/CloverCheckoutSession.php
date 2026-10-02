<?php

namespace Webkul\Clover\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Checkout\Contracts\Cart;
use Webkul\Checkout\Models\CartProxy;
use Webkul\Clover\Contracts\CloverCheckoutSession as CloverCheckoutSessionContract;

class CloverCheckoutSession extends Model implements CloverCheckoutSessionContract
{
    /**
     * Fillable property of the model.
     *
     * @var array
     */
    protected $fillable = [
        'checkout_session_id',
        'cart_id',
        'status',
        'payment_id',
        'base_grand_total',
        'currency_code',
        'verified_via',
    ];

    /**
     * Get the cart that owns the checkout session.
     *
     * @return Cart
     */
    public function cart()
    {
        return $this->belongsTo(CartProxy::modelClass(), 'cart_id');
    }
}
