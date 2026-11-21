<?php

namespace App\Http\Controllers;

use App\Models\PesananDapur;

class DapurController extends Controller
{
    public function index()
    {
        return view('dapur');
    }

    public function data()
    {
        return PesananDapur::orderBy('diterima_pada')
            ->get(['kode', 'menu', 'qty', 'status',
                   'diterima_pada']);
    }

    public function mulai(string $kode)
    {
        $pesanan = PesananDapur::where('kode', $kode)->firstOrFail();

        if ($pesanan->status === 'masuk') {
            $pesanan->update(['status' => 'dimasak']);
        }

        return response()->json(['status' => $pesanan->status]);
    }
}
