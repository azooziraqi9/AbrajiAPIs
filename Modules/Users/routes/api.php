<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\ProfileController;
use Modules\Users\Http\Controllers\UsersController;

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

Route::group(['prefix' => 'users'], function () {
    Route::post('table', [UsersController::class, 'usersTable']);
    Route::post('online', [UsersController::class, 'onlineUsers']);
    Route::post('create', [UsersController::class, 'createUser']);
    Route::put('edit/{id}', [UsersController::class, 'editUser']);
    Route::get('get/{id}', [UsersController::class, 'getUserById']);
    Route::get('disconnect/{id}', [UsersController::class, 'disconnectUser']);
    Route::post('activate', [UsersController::class, 'usersActivate']);
    Route::post('traffic', [\Modules\Users\Http\Controllers\TrafficController::class, 'trafficData']);
    Route::post("sessions/{id}", [\Modules\Users\Http\Controllers\SessionController::class, 'getUserSessions']);
});
// create group for profile
Route::group(['prefix' => 'profile'], function () {
    Route::get('manager-tree', [ProfileController::class, 'getManagerTree']);
    Route::get('services/{id}', [ProfileController::class, 'getServices']);
    Route::post('change-service', [ProfileController::class, 'changeUserService']);
    Route::post('change-profile', [ProfileController::class, 'changeProfile']);
    Route::get('active-data/{id}', [ProfileController::class, 'getActiveData']);

});
