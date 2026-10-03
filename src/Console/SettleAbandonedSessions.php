<?php

namespace Webkul\Clover\Console;

use Illuminate\Console\Command;
use Webkul\Checkout\Facades\Cart;
use Webkul\Clover\Contracts\CloverCheckoutSession;
use Webkul\Clover\Helpers\PaymentProcessor;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;

class SettleAbandonedSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clover:settle-abandoned
        {--minutes= : Only process checkout sessions idle for at least this many minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the orders of paid Clover checkout sessions whose customer never returned to the store';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected CloverCheckoutSessionRepository $cloverCheckoutSessionRepository,
        protected PaymentProcessor $paymentProcessor,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $minutes = (int) ($this->option('minutes') ?: config('services.clover.abandoned_session_minutes', 60));

        $checkoutSessions = $this->cloverCheckoutSessionRepository
            ->findWhere(['status' => CloverCheckoutSession::STATUS_PAID])
            ->filter(fn ($session) => $session->updated_at->lte(now()->subMinutes($minutes)));

        foreach ($checkoutSessions as $checkoutSession) {
            $order = $this->paymentProcessor->findOrderByCartId($checkoutSession->cart_id);

            if (! $order && $cart = $checkoutSession->cart) {
                $order = $this->paymentProcessor->createOrder($cart, $checkoutSession, CloverCheckoutSession::VERIFIED_VIA_WEBHOOK);
            }

            if (! $order) {
                continue;
            }

            if ($cart = $checkoutSession->cart) {
                if ($cart->is_active) {
                    Cart::setCart($cart);

                    Cart::deActivateCart();
                }
            }

            $this->paymentProcessor->settle($order, $checkoutSession->fresh());
        }

        $this->info(sprintf('Settled [%s] abandoned Clover checkout session(s).', $checkoutSessions->count()));

        return self::SUCCESS;
    }
}
