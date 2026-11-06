<?php

namespace Tests\Feature\Tp06;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * TP 06 · Bagian D — Challenge: Peta Lokasi di Halaman Tentang (10 poin)
 *
 * Dijalankan di AKAR REPO. Tidak memanggil layanan apa pun: yang diperiksa
 * hanya isi halaman yang dikirim ke peramban.
 */
class Tp064PetaTest extends TestCase
{
    use RefreshDatabase;

    /** Halaman tentang boleh publik atau di balik login — dua-duanya diterima. */
    private function buka(): TestResponse
    {
        $res = $this->get('/tentang');

        if ($res->isRedirect()) {
            $res = $this->actingAs(User::factory()->create())->get('/tentang');
        }

        return $res;
    }

    public function test_d1_halaman_tentang_terbuka(): void
    {
        $this->buka()->assertOk();
    }

    public function test_d2_memuat_pustaka_leaflet(): void
    {
        $this->buka()->assertSee('leaflet', false);
    }

    public function test_d3_memakai_tile_openstreetmap(): void
    {
        $this->buka()->assertSee('tile.openstreetmap.org', false);
    }

    public function test_d4_atribusi_openstreetmap_terlihat(): void
    {
        $this->buka()->assertSee('OpenStreetMap', false);
    }

    public function test_d5_koordinat_dibaca_dari_config(): void
    {
        config(['services.peta.lat' => -7.12345, 'services.peta.lon' => 108.54321]);

        $this->buka()
            ->assertOk()
            ->assertSee('-7.12345', false)
            ->assertSee('108.54321', false);
    }
}
