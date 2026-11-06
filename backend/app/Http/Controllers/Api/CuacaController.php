<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CuacaService;

class CuacaController extends Controller
{
    public function __invoke(CuacaService $cuaca)
    {
        $data = $cuaca->sekarang();

        if ($data === null) {
            return response()->json([
                'message' => 'Layanan cuaca sedang tidak bisa dihubungi.',
            ], 503);
        }

        return response()->json(['data' => $data]);
    }
}