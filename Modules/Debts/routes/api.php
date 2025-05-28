<?php

use Illuminate\Support\Facades\Route;
use Modules\Debts\Http\Controllers\DebtsController;

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

//make group for all routes
Route::group(['prefix' => 'debts'], function () {
    //get all debts
    Route::post('/', [DebtsController::class, 'getAll']);
    //create new debt
    Route::post('/create', [DebtsController::class, 'create']);
    //get debt by user id
    Route::post('/user/{id}', [DebtsController::class, 'getByUserId']);
    //pay debt by id
    Route::put('/pay/{id}', [DebtsController::class, 'payDebt']);
    //get all debts for user
    Route::post('/user/{id}/total', [DebtsController::class, 'getAllDebtsForUser']);
    //get total debts is paid in my system  send month and year
    Route::post('/total/pay/filter', [DebtsController::class, 'filter']);
    //update debt by id
    Route::put('/update/{id}', [DebtsController::class, 'updateDebt']);
    //create partial payment for debt
    Route::post('/partial-payment/{debtId}', [DebtsController::class, 'createPartialPayment']);
    //get all partial payments for debt
    Route::get('/partial-payment/{debtId}', [DebtsController::class, 'getPartialPayments']);
    //update partial payment for debt
    Route::put('/partial-payment/update/{id}', [DebtsController::class, 'updatePartialPayments']);
    //get some statistics about debts
    Route::get('/statistics', [DebtsController::class, 'getStatistics']);
    //get some statistics about debts for user
    Route::get('/statistics/user/{userId}', [DebtsController::class, 'getUserStatistics']);
});
