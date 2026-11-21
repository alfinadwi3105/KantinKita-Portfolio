<?php

namespace Tests\Feature\Tp08;

use App\Models\PesananDapur;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * TP 08 · Bagian A — Basis Data Milik Dapur Sendiri (25 poin)
 *
 * Dijalankan DI DALAM folder service-kitchen:
 *     php artisan test --filter=Tp081Dapur
 *
 * Yang diperiksa hanya pokoknya: tabelnya ada dengan kolom yang disepakati,
 * modelnya bisa menyimpan, dan message_id-nya benar-benar unik.
 */
class Tp081DapurTest extends TestCase
{
    use RefreshDatabase;

    public function test_a1_tabel_pesanan_dapur_ada_dengan_kolomnya(): void
    {
        $this->assertTrue(Schema::hasTable('pesanan_dapur'),
            'Nama tabelnya "pesanan_dapur", bukan "pesanan_dapurs".');

        $this->assertTrue(Schema::hasColumns('pesanan_dapur', [
            'message_id', 'kode', 'menu_id', 'menu', 'qty', 'status',
            'diterima_pada',
        ]), 'Ada kolom yang belum dibuat di tabel pesanan_dapur.');
    }

    public function test_a2_model_bisa_menyimpan_satu_baris(): void
    {
        PesananDapur::create([
            'message_id'    => (string) Str::uuid(),
            'kode'          => 'KF-UJI-001',
            'menu_id'       => 3,
            'menu'          => 'Nasi Goreng',
            'qty'           => 2,
            'status'        => 'masuk',
            'diterima_pada' => now(),
        ]);

        $this->assertDatabaseHas('pesanan_dapur', [
            'kode'   => 'KF-UJI-001',
            'menu'   => 'Nasi Goreng',
            'status' => 'masuk',
        ]);
    }

    public function test_a3_message_id_tidak_boleh_kembar(): void
    {
        $id = (string) Str::uuid();

        $baris = fn () => PesananDapur::create([
            'message_id'    => $id,
            'kode'          => 'KF-UJI-00'.random_int(1, 9),
            'menu_id'       => 3,
            'menu'          => 'Nasi Goreng',
            'qty'           => 2,
            'status'        => 'masuk',
            'diterima_pada' => now(),
        ]);

        $baris();

        $this->expectException(QueryException::class);
        $baris();
    }
}
