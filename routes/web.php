<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TentangController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/menu')->name('beranda');

Route::get('/tentang', [TentangController::class, 'index'])
    ->name('tentang');

Route::get('/antrian', [AntrianController::class, 'index'])
    ->name('antrian');

Route::get('/antrian/data', [AntrianController::class, 'data'])
    ->name('antrian.data');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');
    Route::view('/peta', 'peta')->name('peta');


    // Route untuk pesanan
    Route::post('/pesanan', [PesananController::class, 'store'])
        ->name('pesanan.store');

    Route::get('/pesanan/{kode}', [PesananController::class, 'show'])
        ->name('pesanan.show');

    Route::resource('menu', MenuController::class)
        ->only(['index', 'show'])
        ->whereNumber('menu');

    Route::middleware('can:manage-menu')->group(function () {
        Route::resource('menu', MenuController::class)
            ->only(['create', 'store', 'edit', 'update'])
            ->whereNumber('menu');
    });

    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])
        ->middleware('can:delete-menu')
        ->whereNumber('menu')
        ->name('menu.destroy');
});