<?php

namespace Webkul\Clover\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Webkul\Clover\Http\Controllers\Controller;
use Webkul\Clover\Payment\Clover;
use Webkul\Clover\Repositories\CloverCheckoutSessionRepository;

class DiagnosticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CloverCheckoutSessionRepository $cloverCheckoutSessionRepository,
        protected Clover $clover,
    ) {}

    /**
     * Shows the checkout sessions audit trail and the recent Clover log
     * entries, so payment failures can be diagnosed from the admin panel
     * without server access.
     *
     * @return View
     */
    public function index()
    {
        $checkoutSessions = $this->cloverCheckoutSessionRepository->scopeQuery(function ($query) {
            return $query->orderBy('id', 'desc')->limit(50);
        })->get();

        $configuration = [
            'sandbox' => $this->clover->getConfigData('sandbox') ? 'ON' : 'OFF',
            'api_token_set' => ! empty($this->clover->getApiKey()) ? 'yes' : 'NO',
            'merchant_id_set' => ! empty($this->clover->getMerchantId()) ? 'yes' : 'NO',
            'webhook_secret_set' => ! empty($this->clover->getWebhookSecret()) ? 'yes' : 'NO',
            'available_at_checkout' => $this->clover->isAvailable() ? 'yes' : 'NO',
        ];

        $logLines = $this->getRecentCloverLogLines();

        return view('clover::admin.diagnostics', compact('checkoutSessions', 'configuration', 'logLines'));
    }

    /**
     * Returns the last complete error entries related to clover, including
     * their full stack traces.
     *
     * @return array
     */
    protected function getRecentCloverLogLines()
    {
        $entries = [];

        foreach (glob(storage_path('logs/laravel*.log')) ?: [] as $file) {
            $content = file_get_contents($file) ?: '';

            $blocks = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $content);

            foreach ($blocks as $block) {
                if (stripos($block, 'clover') === false || stripos($block, '.ERROR') === false) {
                    continue;
                }

                $blockLines = explode("\n", trim($block));

                if (count($blockLines) > 120) {
                    $blockLines = array_merge(array_slice($blockLines, 0, 20), ['… truncated …'], array_slice($blockLines, -100));
                }

                $entries[] = basename($file).': '.implode("\n", $blockLines);
            }
        }

        return array_slice($entries, -5);
    }
}
