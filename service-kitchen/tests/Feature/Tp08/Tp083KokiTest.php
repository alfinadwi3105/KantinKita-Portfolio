<?php

namespace Tests\Feature\Tp08;

use App\Models\PesananDapur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * TP 08 · Bagian C — Consumer dan Layar Koki (35 poin)
 *
 * Dijalankan DI DALAM folder service-kitchen:
 *     php artisan test --filter=Tp083Koki
 *
 * Perintah `rabbit:consume` sendiri tidak diuji mesin — ia memang butuh
 * broker yang menyala. Yang diuji di sini adalah dua route yang menjadi
 * layar kokinya.
 */
class Tp083KokiTest extends TestCase
{
    use RefreshDatabase;

    private function baris(string $kode, string $status, int $menitLalu): void
    {
        PesananDapur::create([
            'message_id'    => (string) Str::uuid(),
            'kode'          => $kode,
            'menu_id'       => 3,
            'menu'          => 'Nasi Goreng',
            'qty'           => 2,
            'status'        => $status,
            'diterima_pada' => now()->subMinutes($menitLalu),
        ]);
    }

    public function test_c1_layar_dapur_terbuka_tanpa_login(): void
    {
        $this->get('/dapur')->assertOk();
    }

    public function test_c2_halaman_depan_mengalihkan_ke_layar_dapur(): void
    {
        $this->get('/')->assertRedirect('/dapur');
    }

    public function test_c3_data_memuat_pesanan_masuk_dan_terurut(): void
    {
        $this->baris('KF-UJI-002', 'masuk', 1);
        $this->baris('KF-UJI-001', 'masuk', 5);

        $res = $this->getJson('/dapur/data');

        $res->assertOk();
        $this->assertCount(2, $res->json());
        $this->assertSame('KF-UJI-001', $res->json('0.kode'),
            'Pesanan yang paling awal diterima harus muncul paling atas.');
    }
}
