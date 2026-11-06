<?php

namespace Tests\Feature\Tp06;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TP 06 · Bagian C — Frontend Memakai Keduanya (35 poin)
 *
 * Dijalankan di AKAR REPO.
 * Backend dipalsukan dengan Http::fake(), jadi tes ini tidak butuh
 * php artisan serve --port=8001 yang menyala.
 */
class Tp063FrontendTest extends TestCase
{
    use RefreshDatabase;

    private const KUNCI_BACKEND = 'rahasia-kantin-fit';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.menu_api.url'        => 'http://backend-palsu.test/api',
            'services.menu_api.key'         => self::KUNCI_BACKEND,
            'services.midtrans.client_key'  => 'SB-Mid-client-UJI',
        ]);
    }

    private function pengguna(): User
    {
        return User::factory()->create();
    }

    private function pesananPalsu(string $status = 'pending'): array
    {
        return ['data' => [
            'kode'       => 'KF-UJI-001',
            'menu'       => 'Nasi Goreng',
            'qty'        => 1,
            'total'      => 18000,
            'status'     => $status,
            'snap_token' => 'tok-abc-123',
        ]];
    }

    private function fakeBackend(int $statusCuaca = 200): void
    {
        Http::fake([
            '*/cuaca'      => Http::response(
                $statusCuaca === 200
                    ? ['data' => ['suhu' => 27.4, 'waktu' => '2026-11-05T08:00']]
                    : ['message' => 'mati'], $statusCuaca),
            '*/categories' => Http::response(['data' => []], 200),
            '*/menus*'     => Http::response(['data' => []], 200),
            '*/pesanan/*'  => Http::response($this->pesananPalsu(), 200),
            '*/pesanan'    => Http::response($this->pesananPalsu(), 201),
            '*'            => Http::response(['data' => []], 200),
        ]);
    }

    public function test_c1_halaman_menu_menampilkan_suhu(): void
    {
        $this->fakeBackend();

        $this->actingAs($this->pengguna())
            ->get('/menu')
            ->assertOk()
            ->assertSee('27.4');
    }

    public function test_c2_halaman_menu_tetap_hidup_saat_cuaca_mati(): void
    {
        $this->fakeBackend(503);

        $this->actingAs($this->pengguna())
            ->get('/menu')
            ->assertOk();
    }

    public function test_c3_halaman_menu_tetap_hidup_saat_koneksi_putus(): void
    {
        Http::fake(function ($req) {
            if (str_contains($req->url(), '/cuaca')) {
                throw new \Illuminate\Http\Client\ConnectionException('putus');
            }

            return Http::response(['data' => []], 200);
        });

        $this->actingAs($this->pengguna())
            ->get('/menu')
            ->assertOk();
    }

    public function test_c4_memesan_mengirim_post_ke_backend(): void
    {
        $this->fakeBackend();

        $this->actingAs($this->pengguna())
            ->post('/pesanan', ['menu_id' => 1, 'qty' => 2])
            ->assertRedirect();

        Http::assertSent(fn ($req) =>
            $req->method() === 'POST'
            && str_contains($req->url(), '/pesanan')
            && $req->hasHeader('X-API-KEY', self::KUNCI_BACKEND));
    }

    public function test_c5_halaman_status_memuat_snapjs_dan_client_key(): void
    {
        $this->fakeBackend();

        $this->actingAs($this->pengguna())
            ->get('/pesanan/KF-UJI-001')
            ->assertOk()
            ->assertSee('snap.js', false)
            ->assertSee('SB-Mid-client-UJI', false)
            ->assertSee('KF-UJI-001', false);
    }

    public function test_c6_halaman_status_memuat_token_untuk_snap_pay(): void
    {
        $this->fakeBackend();

        $this->actingAs($this->pengguna())
            ->get('/pesanan/KF-UJI-001')
            ->assertOk()
            ->assertSee('tok-abc-123', false);
    }

    public function test_c7_kunci_api_backend_tidak_muncul_di_html(): void
    {
        $this->fakeBackend();
        $pengguna = $this->pengguna();

        $this->actingAs($pengguna)->get('/menu')
            ->assertOk()->assertDontSee(self::KUNCI_BACKEND, false);

        $this->actingAs($pengguna)->get('/pesanan/KF-UJI-001')
            ->assertOk()->assertDontSee(self::KUNCI_BACKEND, false);
    }
}
