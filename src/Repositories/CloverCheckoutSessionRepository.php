<?php

namespace Webkul\Clover\Repositories;

use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Core\Eloquent\Repository;

class CloverCheckoutSessionRepository extends Repository
{
    /**
     * This audit table must always be read fresh: the payment flow reads and
     * writes session statuses across webhook, browser and console processes.
     *
     * @param  string  $method
     * @return bool
     */
    public function allowedCache($method)
    {
        return false;
    }

    /**
     * Specify model class name.
     *
     * @return string
     */
    public function model()
    {
        return CloverCheckoutSession::class;
    }

    /**
     * Returns the latest session of the given cart, whatever its state.
     *
     * @param  int  $cartId
     * @return CloverCheckoutSession|null
     */
    public function findLatestForCart($cartId)
    {
        return $this->scopeQuery(function ($query) use ($cartId) {
            return $query->where('cart_id', $cartId)
                ->orderBy('id', 'desc');
        })->first();
    }
}
