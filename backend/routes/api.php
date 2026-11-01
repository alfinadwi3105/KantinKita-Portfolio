<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::apiResource('menus', MenuController::class);
});