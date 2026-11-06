<?php

namespace Tests\Feature\Tp06;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TP 06 · Bagian A — Layanan Cuaca (25 poin)
 *
 * Dijalankan DI DALAM folder backend.
 * Seluruh panggilan keluar dipalsukan dengan Http::fake(), jadi tes ini
 * tidak pernah benar-benar menghubungi Open-Meteo.
 */
class Tp061CuacaTest extends TestCase
{
    use RefreshDatabase;

    private const KUNCI = ['X-API-KEY' => 'rahasia-kantin-fit'];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.api.key' => 'rahasia-kantin-fit']);
    }

    private function fakeSukses(): void
    {
        Http::fake(['*' => Http::response([
            'latitude'  => -6.97,
            'longitude' => 107.63,
            'current'   => [
                'time'           => '2026-11-05T08:00',
                'interval'       => 900,
                'temperature_2m' => 27.4,
            ],
        ], 200)]);
    }

    public function test_a1_endpoint_cuaca_menjawab_200_dengan_suhu(): void
    {
        $this->fakeSukses();

        $res = $this->getJson('/api/cuaca', self::KUNCI);

        $res->assertOk();
        $this->assertIsNumeric(
            $res->json('data.suhu'),
            'Respons harus memuat data.suhu berupa angka.'
        );
    }

    public function test_a2_alamat_layanan_dibaca_dari_config(): void
    {
        config(['services.cuaca.url' => 'https://cuaca-palsu.test/v1']);
        $this->fakeSukses();

        $this->getJson('/api/cuaca', self::KUNCI);

        Http::assertSent(fn ($req) =>
            str_contains($req->url(), 'cuaca-palsu.test'));
    }

    public function test_a3_jawaban_disimpan_di_cache(): void
    {
        $this->fakeSukses();

        $this->getJson('/api/cuaca', self::KUNCI)->assertOk();
        $this->getJson('/api/cuaca', self::KUNCI)->assertOk();

        Http::assertSentCount(1);
    }

    public function test_a4_layanan_gagal_dijawab_503_bukan_500(): void
    {
        Http::fake(['*' => Http::response(['pesan' => 'rusak'], 503)]);

        $this->getJson('/api/cuaca', self::KUNCI)->assertStatus(503);
    }

    public function test_a5_koneksi_putus_juga_dijawab_503(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException(
            'Connection timed out'));

        $this->getJson('/api/cuaca', self::KUNCI)->assertStatus(503);
    }

    public function test_a6_field_mentah_open_meteo_tidak_bocor(): void
    {
        $this->fakeSukses();

        $this->getJson('/api/cuaca', self::KUNCI)
            ->assertOk()
            ->assertDontSee('temperature_2m')
            ->assertDontSee('"current"', false);
    }

    public function test_a7_endpoint_cuaca_tetap_dijaga_kunci_api(): void
    {
        $this->fakeSukses();

        $this->getJson('/api/cuaca')->assertStatus(401);
    }
}
