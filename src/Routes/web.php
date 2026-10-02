<?php

use Illuminate\Support\Facades\Route;
use Webkul\Clover\Http\Controllers\CloverController;

Route::controller(CloverController::class)
    ->middleware('web')
    ->prefix('clover')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('clover.standard.redirect');

        Route::get('success', 'success')->name('clover.payment.success');

        Route::get('cancel', 'cancel')->name('clover.payment.cancel');

        Route::post('webhook', 'webhook')->name('clover.payment.webhook');
    });
