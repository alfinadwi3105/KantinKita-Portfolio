<?php

namespace Tests\Feature\Tp08;

use App\Pesan\PemrosesPesan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * TP 08 · Bagian B — Memproses Pesan dengan Aman (30 poin)
 *
 * Dijalankan DI DALAM folder service-kitchen:
 *     php artisan test --filter=Tp082Pemroses
 *
 * RabbitMQ TIDAK dinyalakan. PemrosesPesan memang tidak pernah menyentuh
 * jaringan — ia hanya menerima sebuah string JSON, persis seperti yang
 * nanti diberikan consumer.
 */
class Tp082PemrosesTest extends TestCase
{
    use RefreshDatabase;

    private function pesan(array $ubah = []): string
    {
        return json_encode(array_merge([
            'message_id'   => (string) Str::uuid(),
            'event'        => 'PesananDibayar',
            'terjadi_pada' => now()->toIso8601String(),
            'data'         => [
                'kode'    => 'KF-UJI-001',
                'menu_id' => 3,
                'menu'    => 'Nasi Goreng',
                'qty'     => 2,
                'total'   => 36000,
            ],
        ], $ubah));
    }

    private function pemroses(): PemrosesPesan
    {
        return app(PemrosesPesan::class);
    }

    public function test_b1_pesan_yang_benar_menjadi_satu_baris(): void
    {
        $this->assertTrue($this->pemroses()->proses($this->pesan()));

        $this->assertDatabaseCount('pesanan_dapur', 1);
        $this->assertDatabaseHas('pesanan_dapur', [
            'kode'   => 'KF-UJI-001',
            'menu'   => 'Nasi Goreng',
            'qty'    => 2,
            'status' => 'masuk',
        ]);
    }

    public function test_b2_pesan_yang_sama_dua_kali_tetap_satu_baris(): void
    {
        $json = $this->pesan();

        $this->pemroses()->proses($json);
        $this->pemroses()->proses($json);

        $this->assertDatabaseCount('pesanan_dapur', 1);
    }

    public function test_b3_pesan_yang_tidak_dikenal_ditolak_tanpa_error(): void
    {
        $p = $this->pemroses();

        $this->assertFalse($p->proses('bukan json sama sekali'));
        $this->assertFalse($p->proses(
            $this->pesan(['event' => 'PesananSelesai'])));
        $this->assertFalse($p->proses(
            json_encode(['event' => 'PesananDibayar'])));

        $this->assertDatabaseCount('pesanan_dapur', 0);
    }
}
