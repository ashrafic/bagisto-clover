<?php

namespace Webkul\Clover\Repositories;

use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Contracts\CloverCheckoutSession as CloverCheckoutSessionContract;
use Webkul\Core\Eloquent\Repository;

class CloverCheckoutSessionRepository extends Repository
{
    /**
     * Specify model class name.
     *
     * @return string
     */
    public function model()
    {
        return CloverCheckoutSessionContract::class;
    }

    /**
     * Returns the latest pending session of the given cart.
     *
     * @param  int  $cartId
     * @return CloverCheckoutSession|null
     */
    public function findLatestPendingForCart($cartId)
    {
        return $this->scopeQuery(function ($query) use ($cartId) {
            return $query->where('cart_id', $cartId)
                ->where('status', CloverCheckoutSessionContract::STATUS_NEW)
                ->orderBy('id', 'desc');
        })->first();
    }
}
