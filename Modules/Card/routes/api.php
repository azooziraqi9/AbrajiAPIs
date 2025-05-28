<?php

use Illuminate\Support\Facades\Route;
use Modules\Card\Http\Controllers\CardController;

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

Route::prefix('card')->group(function () {
    //defining the route for the getAllCards method in the cardController
    Route::post('getAllCards', [CardController::class, 'getAllCards']);
    //defining the route for the getCardById method in the cardController
    Route::get('getCardById/{id}', [CardController::class, 'getCardById']);
    //defining the route for the getListCardForSeries method in the cardController
    Route::post('getListCardForSeries/{id}', [CardController::class, 'getListCardForSeries']);
})->middleware("check-token");
