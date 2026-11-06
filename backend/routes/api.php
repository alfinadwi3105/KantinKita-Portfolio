<?php

use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\CuacaController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::apiResource('menus', MenuController::class);
    Route::get('/cuaca', CuacaController::class);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan/{kode}', [PesananController::class, 'show']);
});
