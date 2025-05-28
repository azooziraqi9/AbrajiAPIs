<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

// endpoint to get dashboard
Route::get('dashboard', [DashboardController::class, 'getDashboard']);
Route::get('dashboard/cards', [DashboardController::class, 'getCardsWidgets']);
Route::get('dashboard/transactions', [DashboardController::class, 'getTransactionsWidgets']);