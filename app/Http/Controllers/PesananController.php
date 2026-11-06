<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PesananController extends Controller
{
    private function api(): PendingRequest
    {
        return Http::baseUrl(config('services.menu_api.url'))
            ->withHeaders([
                'X-API-KEY' => config('services.menu_api.key')
            ])
            ->timeout(10);
    }

    public function store(Request $request)
    {
        $res = $this->api()
            ->post('/pesanan', $request->only('menu_id', 'qty'));

        if ($res->status() === 422) {
            return back()->withInput()
                ->withErrors($res->json('errors'));
        }

        abort_if($res->failed(), 503,
            'Layanan pesanan sedang tidak tersedia.');

        return to_route('pesanan.show', $res->object()->data->kode);
    }

    public function show(string $kode)
    {
        try {
            $res = $this->api()->get('/pesanan/' . $kode);
        } catch (ConnectionException $e) {
            abort(503, 'Layanan pesanan sedang tidak tersedia.');
        }

        abort_if($res->failed(), 503,
            'Layanan pesanan sedang tidak tersedia.');

        return view('pesanan.show', [
            'pesanan' => $res->object()->data
        ]);
    }
}