<?php

namespace App\Http\Controllers\Api;

use App\Models\Antrian;
use App\Http\Controllers\Controller;
use App\Http\Resources\PesananResource;
use App\Broker\Penerbit;
use App\Pesan\PesanPesananDibayar;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    public function store(Request $request, MidtransService $midtrans)
    {
        $data = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $menu = Menu::findOrFail($data['menu_id']);

        $pesanan = Pesanan::create([
            'kode' => $this->kodeBaru(),
            'menu_id' => $menu->id,
            'qty' => $data['qty'],
            'total' => $menu->price * $data['qty'],
            'status' => 'pending',
        ]);

        $pesanan->snap_token = $midtrans->buatTransaksi($pesanan);
        $pesanan->save();

        return (new PesananResource($pesanan))
            ->response()->setStatusCode(201);
    }

    private function kodeBaru(): string
    {
        return config('services.midtrans.prefix')
            . '-' . now()->format('ymdHis')
            . '-' . Str::upper(Str::random(3));
    }

    public function show(string $kode, MidtransService $midtrans)
    {
        $pesanan = Pesanan::where('kode', $kode)->firstOrFail();

        $status = $midtrans->statusDari($pesanan->kode);

        if ($status !== null) {
            $pesanan->status = MidtransService::petakan($status);
            $pesanan->save();

            if ($pesanan->status === 'lunas') {
                $this->masukkanAntrian($pesanan);
            }
        }

        return new PesananResource($pesanan);
    }

    private function masukkanAntrian(Pesanan $pesanan): Antrian
    {
        $antrian = Antrian::firstOrCreate(
            ['kode' => $pesanan->kode],
            [
                'menu_id'      => $pesanan->menu_id,
                'menu'         => $pesanan->menu->name,
                'qty'          => $pesanan->qty,
                'total'        => $pesanan->total,
                'status'       => 'diproses',
                'dipesan_pada' => now(),
            ]);

        if ($antrian->wasRecentlyCreated) {
            app(Penerbit::class)->terbitkan(
                PesanPesananDibayar::dari($antrian));
        }

        return $antrian;
    }
}