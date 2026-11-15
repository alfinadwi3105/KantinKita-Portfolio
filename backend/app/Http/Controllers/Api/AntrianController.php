<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Antrian;

class AntrianController extends Controller
{
    public function index()
    {
        return Antrian::orderBy('dipesan_pada')
            ->get(['kode', 'menu', 'qty', 'status', 'dipesan_pada']);
    }
}