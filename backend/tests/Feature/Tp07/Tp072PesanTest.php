<?php

namespace Tests\Feature\Tp07;

use App\Broker\Penerbit;
use App\Broker\PenerbitUji;
use App\Models\Antrian;
use App\Pesan\PesanPesananDibayar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * TP 07 · Bagian B — Kontrak Pesan dan Penerbit (30 poin)
 *
 * Dijalankan DI DALAM folder backend:
 *     php artisan test --filter=Tp072Pesan
 *
 * Bagian ini hanya memeriksa dua hal: bentuk pesannya sudah sesuai kontrak,
 * dan penerbitnya bisa diganti tanpa menyentuh kode pemanggilnya. Soal
 * kapan pesannya terbit diuji di Bagian C.
 */
class Tp072PesanTest extends TestCase
{
    use RefreshDatabase;

    private function antrian(): Antrian
    {
        return Antrian::create([
            'kode'         => 'KF-UJI-001',
            'menu_id'      => 3,
            'menu'         => 'Nasi Goreng',
            'qty'          => 2,
            'total'        => 36000,
            'status'       => 'diproses',
            'dipesan_pada' => now(),
        ]);
    }

    public function test_b1_bentuk_pesannya_sesuai_kontrak(): void
    {
        $pesan = PesanPesananDibayar::dari($this->antrian());

        $this->assertSame('PesananDibayar', $pesan['event']);
        $this->assertArrayHasKey('message_id', $pesan);
        $this->assertArrayHasKey('terjadi_pada', $pesan);
        $this->assertArrayHasKey('data', $pesan);

        foreach (['kode', 'menu_id', 'menu', 'qty', 'total'] as $bidang) {
            $this->assertArrayHasKey($bidang, $pesan['data'],
                "Bidang data.$bidang belum ada di dalam pesan.");
        }
    }

    public function test_b2_message_id_selalu_baru(): void
    {
        $antrian = $this->antrian();

        $satu = PesanPesananDibayar::dari($antrian)['message_id'];
        $dua  = PesanPesananDibayar::dari($antrian)['message_id'];

        $this->assertNotSame($satu, $dua,
            'message_id harus berbeda setiap kali pesan disusun.');
        $this->assertTrue(Str::isUuid($satu),
            'message_id sebaiknya berupa UUID.');
    }

    public function test_b3_angka_dikirim_sebagai_angka(): void
    {
        $data = PesanPesananDibayar::dari($this->antrian())['data'];

        $this->assertIsInt($data['menu_id']);
        $this->assertIsInt($data['qty']);
        $this->assertIsInt($data['total']);
    }

    public function test_b4_penerbit_dipilih_lewat_config_broker_driver(): void
    {
        config(['broker.driver' => 'uji']);

        $penerbit = app(Penerbit::class);

        $this->assertInstanceOf(PenerbitUji::class, $penerbit,
            'AppServiceProvider belum memilih PenerbitUji saat driver uji.');
    }
}
