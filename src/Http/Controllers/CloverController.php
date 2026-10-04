<?php

namespace Webkul\Clover\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Contracts\CloverCheckoutSession as CloverCheckoutSessionContract;
use Webkul\Clover\Helpers\PaymentProcessor;
use Webkul\Clover\Payment\Clover;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;

class CloverController extends Controller
{
    /**
     * Webhook status that marks a payment as approved.
     *
     * @var string
     */
    public const WEBHOOK_STATUS_APPROVED = 'APPROVED';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CloverCheckoutSessionRepository $cloverCheckoutSessionRepository,
        protected PaymentProcessor $paymentProcessor,
        protected Clover $clover,
    ) {}

    /**
     * Redirects to the Clover hosted checkout page.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        if (! $this->clover->hasValidCredentials()) {
            session()->flash('error', trans('clover::app.response.provide-credentials'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (! $cart) {
            session()->flash('error', trans('clover::app.response.cart-not-found'));

            return redirect()->route('shop.checkout.cart.index');
        }

        try {
            $checkoutSession = $this->clover->createCheckoutSession($cart);

            if (! $checkoutSession) {
                session()->flash('error', trans('clover::app.response.payment-failed'));

                return redirect()->route('shop.checkout.cart.index');
            }

            $this->cloverCheckoutSessionRepository->create([
                'cart_id' => $cart->id,
                'checkout_session_id' => $checkoutSession->checkoutSessionId,
                'base_grand_total' => $cart->base_grand_total,
                'currency_code' => core()->getBaseCurrencyCode(),
                'status' => CloverCheckoutSessionContract::STATUS_NEW,
            ]);

            return redirect($checkoutSession->href);
        } catch (\Throwable $e) {
            report($e);

            session()->flash('error', trans('clover::app.response.payment-failed'));

            return redirect()->route('shop.checkout.cart.index');
        }
    }

    /**
     * Handle the success redirect back from the Clover hosted checkout page.
     * Following Bagisto's standard payment flow, the order is created here in
     * the customer's own request, so the cart session is cleared naturally.
     *
     * @return RedirectResponse
     */
    public function success()
    {
        try {
            $checkoutSession = $this->resolveCheckoutSession();

            if (! $checkoutSession) {
                session()->flash('error', trans('clover::app.response.session-not-found'));

                return redirect()->route('shop.checkout.cart.index');
            }

            if (in_array($checkoutSession->status, [
                CloverCheckoutSessionContract::STATUS_FAILED,
                CloverCheckoutSessionContract::STATUS_CANCELED,
            ])) {
                session()->flash('error', trans('clover::app.response.payment-failed'));

                return redirect()->route('shop.checkout.cart.index');
            }

            if ($checkoutSession->status === CloverCheckoutSessionContract::STATUS_NEW) {
                $this->awaitWebhook($checkoutSession);

                $checkoutSession = $checkoutSession->fresh();
            }

            $verifiedVia = $checkoutSession->status === CloverCheckoutSessionContract::STATUS_PAID
                ? CloverCheckoutSessionContract::VERIFIED_VIA_WEBHOOK
                : CloverCheckoutSessionContract::VERIFIED_VIA_REDIRECT;

            $order = $this->paymentProcessor->findOrderByCartId($checkoutSession->cart_id);

            if (! $order) {
                $cart = Cart::getCart() ?: $checkoutSession->cart;

                if (! $cart) {
                    session()->flash('error', trans('clover::app.response.verification-failed'));

                    return redirect()->route('shop.checkout.cart.index');
                }

                $order = $this->paymentProcessor->createOrder($cart, $checkoutSession, $verifiedVia);

                if ($cart->is_active) {
                    Cart::setCart($cart);

                    Cart::deActivateCart();
                }
            } else {
                if ($cart = $checkoutSession->cart) {
                    if ($cart->is_active) {
                        Cart::setCart($cart);

                        Cart::deActivateCart();
                    }
                }

                if ($checkoutSession->status !== CloverCheckoutSessionContract::STATUS_PROCESSED) {
                    $this->paymentProcessor->markProcessed($checkoutSession, $verifiedVia);
                }
            }

            $order = $this->paymentProcessor->settle($order, $checkoutSession->fresh());

            session()->flash('order_id', $order->id);

            session()->flash('success', trans('clover::app.response.payment-success'));

            return redirect()->route('shop.checkout.onepage.success');
        } catch (\Throwable $e) {
            report($e);

            session()->flash('error', trans('clover::app.response.verification-failed'));

            return redirect()->route('shop.checkout.cart.index');
        }
    }

    /**
     * Handle the failure redirect back from the Clover hosted checkout page.
     *
     * @return RedirectResponse
     */
    public function cancel()
    {
        if ($checkoutSession = $this->resolveCheckoutSession()) {
            $this->cloverCheckoutSessionRepository->update([
                'status' => CloverCheckoutSessionContract::STATUS_FAILED,
            ], $checkoutSession->id);
        }

        session()->flash('error', trans('clover::app.response.payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Handle the Clover hosted checkout webhook. Like Bagisto's PayPal IPN
     * listener, it settles an already created order; it only falls back to
     * creating the order itself when the customer never made it back.
     *
     * @return Response
     */
    public function webhook()
    {
        $payload = request()->getContent();

        if (! $this->clover->getWebhookSecret()) {
            Log::error('Clover webhook rejected: no webhook signing secret is configured for the active channel.');

            return response(trans('clover::app.response.webhook-secret-missing'), Response::HTTP_UNAUTHORIZED);
        }

        if (! $this->clover->verifyWebhookSignature(request()->header('Clover-Signature'), $payload)) {
            return response('Invalid signature', Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($payload, true);

        $checkoutSessionId = $this->extractCheckoutSessionId($data);

        if (! $checkoutSessionId) {
            return response('OK');
        }

        $checkoutSession = $this->cloverCheckoutSessionRepository->findOneByField('checkout_session_id', $checkoutSessionId);

        if (! $checkoutSession) {
            return response('OK');
        }

        $paymentId = $data['id'] ?? null;

        if (($data['status'] ?? null) !== self::WEBHOOK_STATUS_APPROVED) {
            if ($checkoutSession->status === CloverCheckoutSessionContract::STATUS_NEW) {
                $this->cloverCheckoutSessionRepository->update([
                    'status' => CloverCheckoutSessionContract::STATUS_FAILED,
                ], $checkoutSession->id);
            }

            return response('OK');
        }

        if ($checkoutSession->status === CloverCheckoutSessionContract::STATUS_NEW) {
            $this->cloverCheckoutSessionRepository->update([
                'status' => CloverCheckoutSessionContract::STATUS_PAID,
                'payment_id' => $paymentId,
                'verified_via' => CloverCheckoutSessionContract::VERIFIED_VIA_WEBHOOK,
            ], $checkoutSession->id);

            $checkoutSession = $checkoutSession->fresh();
        }

        try {
            if ($order = $this->paymentProcessor->findOrderByCartId($checkoutSession->cart_id)) {
                if ($paymentId && ! $checkoutSession->payment_id) {
                    $this->cloverCheckoutSessionRepository->update(['payment_id' => $paymentId], $checkoutSession->id);
                }

                $this->paymentProcessor->settle($order, $checkoutSession->fresh());
            }
        } catch (\Throwable $e) {
            report($e);

            return response('Processing error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response('OK');
    }

    /**
     * Resolve the checkout session of the current request, first from the
     * session identifier parameter, then from the customer's active cart.
     *
     * @return CloverCheckoutSession|null
     */
    protected function resolveCheckoutSession()
    {
        $sessionId = request()->get('session_id');

        if ($sessionId && $sessionId !== '{CHECKOUT_SESSION_ID}') {
            if ($checkoutSession = $this->cloverCheckoutSessionRepository->findOneByField('checkout_session_id', $sessionId)) {
                return $checkoutSession;
            }
        }

        if ($cart = Cart::getCart()) {
            return $this->cloverCheckoutSessionRepository->findLatestOpenForCart($cart->id);
        }

        return null;
    }

    /**
     * Wait briefly for the webhook to mark the checkout session as paid.
     *
     * @param  CloverCheckoutSession  $checkoutSession
     * @return void
     */
    protected function awaitWebhook($checkoutSession)
    {
        $waitSeconds = (int) config('services.clover.webhook_wait', 6);

        $attempts = $waitSeconds * 2;

        for ($i = 0; $i < $attempts; $i++) {
            if ($checkoutSession->fresh()->status !== CloverCheckoutSessionContract::STATUS_NEW) {
                return;
            }

            usleep(500000);
        }
    }

    /**
     * Extract the checkout session identifier from the webhook payload.
     *
     * @param  array  $data
     * @return string|null
     */
    protected function extractCheckoutSessionId($data)
    {
        if (empty($data['data'])) {
            return null;
        }

        if (is_string($data['data'])) {
            return $data['data'];
        }

        return $data['data']['checkoutSessionId'] ?? null;
    }
}
