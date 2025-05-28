<?php

use Illuminate\Support\Facades\Route;
use Modules\Manager\Http\Controllers\ManagerController;


Route::get('/get-manager', [ManagerController::class, 'GetManger']);


