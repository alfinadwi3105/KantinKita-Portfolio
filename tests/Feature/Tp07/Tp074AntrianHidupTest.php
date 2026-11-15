<?php

namespace Tests\Feature\Tp07;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TP 07 · Bagian D — Challenge: Halaman Antrian yang Hidup (10 poin)
 *
 * Dijalankan DI AKAR REPO (aplikasi tampilan kasir):
 *     php artisan test --filter=Tp074AntrianHidup
 *
 * Backend TIDAK perlu menyala: panggilannya dipalsukan dengan Http::fake().
 */
class Tp074AntrianHidupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.menu_api.url' => 'http://backend-palsu.test/api',
            'services.menu_api.key' => 'rahasia-kantin-fit',
        ]);
    }

    private function isiAntrian(): array
    {
        return [[
            'kode'         => 'KF-UJI-001',
            'menu'         => 'Nasi Goreng',
            'qty'          => 2,
            'status'       => 'diproses',
            'dipesan_pada' => '2026-11-10T10:00:00+07:00',
        ]];
    }

    public function test_d1_endpoint_data_terbuka_untuk_tamu(): void
    {
        Http::fake(['*/antrian' => Http::response($this->isiAntrian(), 200)]);

        $this->getJson('/antrian/data')->assertOk();
    }

    public function test_d2_isinya_meneruskan_jawaban_backend(): void
    {
        Http::fake(['*/antrian' => Http::response($this->isiAntrian(), 200)]);

        $res = $this->getJson('/antrian/data');

        $res->assertOk();
        $this->assertCount(1, $res->json());
        $this->assertSame('KF-UJI-001', $res->json('0.kode'));
        $this->assertSame('diproses', $res->json('0.status'));
    }

    public function test_d3_kunci_api_ikut_dikirim_ke_backend(): void
    {
        Http::fake(['*/antrian' => Http::response($this->isiAntrian(), 200)]);

        $this->getJson('/antrian/data')->assertOk();

        Http::assertSent(fn ($req) =>
            $req->hasHeader('X-API-KEY', 'rahasia-kantin-fit'));
    }

    public function test_d4_backend_mati_tidak_membuat_halaman_ikut_mati(): void
    {
        Http::fake(fn () => throw new ConnectionException('mati'));

        $res = $this->getJson('/antrian/data');

        $res->assertOk();
        $this->assertSame([], $res->json());
    }
}
