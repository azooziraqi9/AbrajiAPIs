<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\WalletController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::group(['prefix' => 'wallet'], function () {
    Route::get('balance', [WalletController::class, 'getMyWalletBalance']);
    Route::post('add-money', [WalletController::class, 'addMoneyToWallet']);
    Route::put('update-balance', [WalletController::class, 'updateWalletAmount']);
    Route::get('reset-balance', [WalletController::class, 'resetWalletAmount']);
    Route::post('transactions', [WalletController::class, 'getWalletTransactions']);
});
