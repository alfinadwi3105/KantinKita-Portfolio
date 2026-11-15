<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class AntrianController extends Controller
{
    public function index()
    {
        try {
            $res = Http::baseUrl(config('services.menu_api.url'))
                ->withHeaders([
                    'X-API-KEY' => config('services.menu_api.key'),
                ])
                ->timeout(10)
                ->get('/antrian');
        } catch (ConnectionException $e) {
            return view('antrian', ['daftar' => [], 'mati' => true]);
        }
        return view('antrian', [
            'daftar' => $res->successful() ? $res->json() : [],
            'mati' => ! $res->successful(),
        ]);
    }

    public function data()
    {
        try {
            $res = Http::baseUrl(config('services.menu_api.url'))
                ->withHeaders([
                    'X-API-KEY' => config('services.menu_api.key'),
                ])
                ->timeout(10)
                ->get('/antrian');
        } catch (ConnectionException $e) {
            return response()->json([]);
        }

        return response()->json($res->successful()
            ? $res->json() : []);
    }
}