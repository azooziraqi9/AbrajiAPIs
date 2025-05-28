<?php

use App\Http\Controllers\SeedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/seed-all', [SeedController::class, 'seedAllModules']);
Route::delete('/delete-all', [SeedController::class, 'deleteAllData']);

