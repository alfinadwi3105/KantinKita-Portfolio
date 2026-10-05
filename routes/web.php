<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\TentangController;
use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])
    ->name('beranda');

Route::get('/tentang', [TentangController::class, 'index'])
    ->name('tentang');

Route::resource('menu', MenuController::class)
    ->whereNumber('menu');