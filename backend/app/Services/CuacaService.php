<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CuacaService
{
    public function sekarang(): ?array
    {
        $ttl = (int) config('services.cuaca.ttl');

        return Cache::remember('cuaca:kantin-fit', $ttl, function () {
            try {
                $res = Http::baseUrl(config('services.cuaca.url'))
                    ->timeout(5)
                    ->retry(2, 200)
                    ->get('/forecast', [
                        'latitude' => config('services.cuaca.lat'),
                        'longitude' => config('services.cuaca.lon'),
                        'current'   => 'temperature_2m',
                    ]);
            } catch (ConnectionException|RequestException $e) {
                return null;
            }

            return [
                'suhu' => $res->json('current.temperature_2m'),
                'waktu' => $res->json('current.time'),
            ];
        });
    }
}