<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoice\Http\Controllers\InvoiceController;

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


Route::prefix('invoices')->group(function () {
    Route::post('/all', [InvoiceController::class, 'index']);       // GET /api/invoices
    Route::get('/{id}', [InvoiceController::class, 'show']);    // GET /api/invoices/{id}
    Route::get('/{id}/approve', [InvoiceController::class, 'approve']);    // GET /api/invoices/{id}
    Route::post('/', [InvoiceController::class, 'store']);      // POST /api/invoices
    Route::put('/{id}', [InvoiceController::class, 'update']);  // PUT /api/invoices/{id}
    Route::delete('/{id}', [InvoiceController::class, 'destroy']); // DELETE /api/invoices/{id}
});
