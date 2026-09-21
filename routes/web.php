<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\TentangController;
use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;
 
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/tentang', [App\Http\Controllers\TentangController::class, 'index'])->name('tentang');
Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu');