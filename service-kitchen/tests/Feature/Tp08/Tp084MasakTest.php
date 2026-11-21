<?php

namespace Tests\Feature\Tp08;

use App\Models\PesananDapur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * TP 08 · Bagian D — Challenge: Tombol Mulai Masak (10 poin)
 *
 * Dijalankan DI DALAM folder service-kitchen:
 *     php artisan test --filter=Tp084Masak
 */
class Tp084MasakTest extends TestCase
{
    use RefreshDatabase;

    private function baris(string $status = 'masuk'): PesananDapur
    {
        return PesananDapur::create([
            'message_id'    => (string) Str::uuid(),
            'kode'          => 'KF-UJI-001',
            'menu_id'       => 3,
            'menu'          => 'Nasi Goreng',
            'qty'           => 2,
            'status'        => $status,
            'diterima_pada' => now(),
        ]);
    }

    public function test_d1_tombol_mengubah_masuk_menjadi_dimasak(): void
    {
        $this->baris('masuk');

        $this->postJson('/dapur/KF-UJI-001/mulai')->assertOk();

        $this->assertDatabaseHas('pesanan_dapur', [
            'kode'   => 'KF-UJI-001',
            'status' => 'dimasak',
        ]);
    }

    public function test_d2_kode_yang_tidak_ada_dijawab_404(): void
    {
        $this->postJson('/dapur/KF-TIDAK-ADA/mulai')->assertStatus(404);
    }

    public function test_d3_baris_yang_sudah_dimasak_tidak_berubah_lagi(): void
    {
        $this->baris('dimasak');

        $this->postJson('/dapur/KF-UJI-001/mulai')->assertOk();

        $this->assertDatabaseHas('pesanan_dapur', [
            'kode'   => 'KF-UJI-001',
            'status' => 'dimasak',
        ]);
        $this->assertDatabaseCount('pesanan_dapur', 1);
    }

    public function test_d4_data_tetap_memuat_baris_yang_sedang_dimasak(): void
    {
        $this->baris('dimasak');

        $res = $this->getJson('/dapur/data');

        $res->assertOk();
        $this->assertSame('dimasak', $res->json('0.status'));
    }
}
