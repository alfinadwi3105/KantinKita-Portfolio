<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    private function api(string $alamat): PendingRequest
    {
        return Http::baseUrl($alamat)
            ->withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(10);
    }

    public function buatTransaksi(Pesanan $pesanan): ?string
    {
        $res = $this->api(config('services.midtrans.snap_url'))
            ->post('/transactions', [
                'transaction_details' => [
                    'order_id' => $pesanan->kode,
                    'gross_amount' => $pesanan->total,
                ],
            ]);

        return $res->successful() ? $res->json('token') : null;
    }

    public function statusDari(string $kode): ?string
    {
        $res = $this->api(config('services.midtrans.api_url'))
            ->get("/v2/{$kode}/status");

        return $res->successful()
            ? $res->json('transaction_status') : null;
    }

    public static function petakan(string $status): string
    {
        return match ($status) {
            'settlement', 'capture' => 'lunas',
            'expire' => 'expired',
            'deny', 'cancel' => 'gagal',
            default => 'pending',
        };
    }
}
