<?php

namespace Webkul\Clover\Helpers;

use Webkul\Checkout\Contracts\Cart;
use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderTransactionRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentProcessor
{
    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected CloverCheckoutSessionRepository $cloverCheckoutSessionRepository,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected OrderTransactionRepository $orderTransactionRepository,
    ) {}

    /**
     * Create the order of a paid checkout session from the given cart.
     *
     * @param  Cart  $cart
     * @param  CloverCheckoutSession  $checkoutSession
     * @param  string  $verifiedVia
     * @return Order
     */
    public function createOrder($cart, $checkoutSession, $verifiedVia)
    {
        $additional = [
            'clover_checkout_session_id' => $checkoutSession->checkout_session_id,
            'clover_verified_via' => $verifiedVia,
        ];

        if ($checkoutSession->payment_id) {
            $additional['clover_payment_id'] = $checkoutSession->payment_id;
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        $data['payment']['additional'] = $additional;

        $order = $this->orderRepository->create($data);

        $this->markProcessed($checkoutSession, $verifiedVia);

        return $order;
    }

    /**
     * Settle the order of a paid checkout session: move it to processing and
     * create the invoice and transaction. Safe to run more than once.
     *
     * @param  Order  $order
     * @param  CloverCheckoutSession  $checkoutSession
     * @return Order
     */
    public function settle($order, $checkoutSession)
    {
        $order = $this->orderRepository->update(['status' => 'processing'], $order->id);

        if (! $order->canInvoice()) {
            return $order;
        }

        $invoiceData = [
            'order_id' => $order->id,
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        $invoice = $this->invoiceRepository->create($invoiceData);

        $this->orderTransactionRepository->create([
            'transaction_id' => $checkoutSession->checkout_session_id,
            'status' => 'APPROVED',
            'type' => $order->payment->method,
            'payment_method' => $order->payment->method,
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'amount' => $order->base_grand_total,
            'data' => json_encode($order->payment->additional ?? []),
        ]);

        return $order;
    }

    /**
     * Mark the checkout session as processed.
     *
     * @param  CloverCheckoutSession  $checkoutSession
     * @param  string  $verifiedVia
     * @return void
     */
    public function markProcessed($checkoutSession, $verifiedVia)
    {
        $this->cloverCheckoutSessionRepository->update([
            'status' => CloverCheckoutSession::STATUS_PROCESSED,
            'verified_via' => $verifiedVia,
        ], $checkoutSession->id);
    }

    /**
     * Returns the order created from the given cart, if any.
     *
     * @param  int|null  $cartId
     * @return Order|null
     */
    public function findOrderByCartId($cartId)
    {
        if (! $cartId) {
            return null;
        }

        return $this->orderRepository->findOneByField('cart_id', $cartId);
    }
}
