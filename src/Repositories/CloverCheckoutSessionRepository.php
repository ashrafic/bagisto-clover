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
     * Returns the latest open session of the given cart.
     *
     * @param  int  $cartId
     * @return CloverCheckoutSession|null
     */
    public function findLatestOpenForCart($cartId)
    {
        return $this->scopeQuery(function ($query) use ($cartId) {
            return $query->where('cart_id', $cartId)
                ->whereIn('status', [
                    CloverCheckoutSession::STATUS_NEW,
                    CloverCheckoutSession::STATUS_PAID,
                ])
                ->orderBy('id', 'desc');
        })->first();
    }
}
