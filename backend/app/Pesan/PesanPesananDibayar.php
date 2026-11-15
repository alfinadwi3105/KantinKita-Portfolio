<?php

namespace App\Pesan;

use App\Models\Antrian;
use Illuminate\Support\Str;

class PesanPesananDibayar
{
    public static function dari(Antrian $antrian): array
    {
        return [
            'message_id'   => (string) Str::uuid(),
            'event'        => 'PesananDibayar',
            'terjadi_pada' => now()->toIso8601String(),
            'data'         => [
                'kode'    => $antrian->kode,
                'menu_id' => (int) $antrian->menu_id,
                'menu'    => $antrian->menu,
                'qty'     => (int) $antrian->qty,
                'total'   => (int) $antrian->total,
            ],
        ];
    }
}
