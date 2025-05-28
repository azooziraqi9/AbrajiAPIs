<?php

use Illuminate\Support\Facades\Route;
use Modules\Transaction\Http\Controllers\TransactionController;

Route::prefix('transactions')->group(function () {
    Route::post('/all', [TransactionController::class, 'index']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('{id}', [TransactionController::class, 'show']);
    Route::put('{id}', [TransactionController::class, 'update']);
    Route::delete('{id}', [TransactionController::class, 'destroy']);
});
