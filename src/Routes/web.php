<?php

use Illuminate\Support\Facades\Route;
use Webkul\Clover\Http\Controllers\Admin\DiagnosticsController;
use Webkul\Clover\Http\Controllers\CloverController;

Route::controller(CloverController::class)
    ->middleware('web')
    ->prefix('clover')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('clover.standard.redirect');

        Route::get('success', 'success')->name('clover.payment.success');

        Route::get('cancel', 'cancel')->name('clover.payment.cancel');
    });

Route::post('clover/webhook', [CloverController::class, 'webhook'])
    ->middleware('throttle:60,1')
    ->name('clover.payment.webhook');

Route::prefix(config('app.admin_url'))
    ->middleware(['web', 'admin'])
    ->group(function () {
        Route::get('clover/diagnostics', [DiagnosticsController::class, 'index'])
            ->name('clover.admin.diagnostics');
    });
