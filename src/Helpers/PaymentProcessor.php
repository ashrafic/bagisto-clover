<?php

namespace Webkul\Clover\Helpers;

use Webkul\Checkout\Facades\Cart;
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
     * Create the order, invoice and transaction of a paid checkout session.
     *
     * @param  CloverCheckoutSession  $checkoutSession
     * @param  string  $verifiedVia
     * @return Order|null
     */
    public function process($checkoutSession, $verifiedVia)
    {
        $checkoutSession = $this->cloverCheckoutSessionRepository->find($checkoutSession->id);

        if (! $checkoutSession) {
            return null;
        }

        if ($checkoutSession->status === CloverCheckoutSession::STATUS_PROCESSED) {
            return $this->findOrderByCartId($checkoutSession->cart_id);
        }

        $cart = $checkoutSession->cart;

        if (! $cart || ! $cart->is_active) {
            $order = $this->findOrderByCartId($checkoutSession->cart_id);

            if ($order) {
                $this->cloverCheckoutSessionRepository->update([
                    'status' => CloverCheckoutSession::STATUS_PROCESSED,
                ], $checkoutSession->id);
            }

            return $order;
        }

        if ($order = $this->findOrderByCartId($cart->id)) {
            $this->cloverCheckoutSessionRepository->update([
                'status' => CloverCheckoutSession::STATUS_PROCESSED,
                'verified_via' => $checkoutSession->verified_via ?? $verifiedVia,
            ], $checkoutSession->id);

            return $order;
        }

        Cart::setCart($cart);

        Cart::collectTotals();

        $cart = Cart::getCart();

        if (! $cart) {
            return null;
        }

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

        $this->orderRepository->update(['status' => 'processing'], $order->id);

        $this->cloverCheckoutSessionRepository->update([
            'status' => CloverCheckoutSession::STATUS_PROCESSED,
            'verified_via' => $verifiedVia,
        ], $checkoutSession->id);

        if ($order->canInvoice()) {
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
                'data' => json_encode($additional),
            ]);
        }

        Cart::deActivateCart();

        return $order->refresh();
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
