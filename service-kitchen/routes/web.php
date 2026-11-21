<?php

use App\Http\Controllers\DapurController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dapur'));
Route::get('/dapur', [DapurController::class, 'index'])
    ->name('dapur');
Route::get('/dapur/data', [DapurController::class, 'data'])
    ->name('dapur.data');
Route::post('/dapur/{kode}/mulai', [DapurController::class, 'mulai'])
    ->name('dapur.mulai');