<?php

namespace App\Pesan;

use App\Models\PesananDapur;

class PemrosesPesan
{
    public function proses(string $json): bool
    {
        $pesan = json_decode($json, true);

        if (! is_array($pesan)
            || ! isset($pesan['message_id'], $pesan['event'],
                       $pesan['data'])) {
            return false;
        }

        if ($pesan['event'] !== 'PesananDibayar') {
            return false;
        }

        $data = $pesan['data'];

        PesananDapur::firstOrCreate(
            ['message_id' => $pesan['message_id']],
            [
                'kode'          => $data['kode'],
                'menu_id'       => $data['menu_id'],
                'menu'          => $data['menu'],
                'qty'           => $data['qty'],
                'status'        => 'masuk',
                'diterima_pada' => now(),
            ]);

        return true;
    }
}
