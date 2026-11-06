<?php

namespace Tests\Feature\Tp06;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TP 06 · Bagian B — Pesanan dan Pembayaran (30 poin)
 *
 * Dijalankan DI DALAM folder backend.
 * Panggilan ke Midtrans dipalsukan, jadi tes ini tidak butuh kunci sandbox
 * yang sungguhan maupun koneksi internet.
 */
class Tp062PesananTest extends TestCase
{
    use RefreshDatabase;

    private const KUNCI = ['X-API-KEY' => 'rahasia-kantin-fit'];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.api.key'              => 'rahasia-kantin-fit',
            'services.midtrans.server_key'  => 'SB-Mid-server-UJI',
            'services.midtrans.snap_url'    => 'https://snap-palsu.test/snap/v1',
            'services.midtrans.api_url'     => 'https://api-palsu.test',
            'services.midtrans.prefix'      => 'KF-UJI',
        ]);
    }

    private function menu(int $harga = 18000): Menu
    {
        $kategori = Category::create(['name' => 'Makanan']);

        return Menu::create([
            'category_id' => $kategori->id,
            'name'        => 'Nasi Goreng',
            'price'       => $harga,
            'stock'       => 10,
        ]);
    }

    private function fakeMidtrans(string $status = 'settlement'): void
    {
        Http::fake([
            '*/transactions' => Http::response(['token' => 'tok-abc-123'], 201),
            '*/status'       => Http::response(
                ['transaction_status' => $status, 'fraud_status' => 'accept'], 200),
            '*'              => Http::response([], 200),
        ]);
    }

    private function buat(Menu $menu, int $qty = 2): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/pesanan',
            ['menu_id' => $menu->id, 'qty' => $qty], self::KUNCI);
    }

    public function test_b1_membuat_pesanan_menjawab_201_dan_menyimpan(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans();

        $res = $this->buat($menu);

        $res->assertStatus(201);
        $this->assertSame('tok-abc-123', $res->json('data.snap_token'));
        $this->assertSame('pending', $res->json('data.status'));
        $this->assertDatabaseCount('pesanan', 1);
    }

    public function test_b2_total_dihitung_dari_database_bukan_dari_request(): void
    {
        $menu = $this->menu(18000);
        $this->fakeMidtrans();

        $res = $this->postJson('/api/pesanan', [
            'menu_id' => $menu->id,
            'qty'     => 2,
            'total'   => 1000,     // harga palsu dari pengirim
            'price'   => 1000,
        ], self::KUNCI);

        $this->assertSame(36000, (int) $res->json('data.total'));
    }

    public function test_b3_nomor_pesanan_berawalan_prefix_dari_config(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans();

        $res = $this->buat($menu);

        $this->assertStringStartsWith('KF-UJI', (string) $res->json('data.kode'));

        Http::assertSent(fn ($req) =>
            str_contains($req->url(), '/transactions')
            && str_starts_with(
                $req['transaction_details']['order_id'] ?? '', 'KF-UJI'));
    }

    public function test_b4_nomor_pesanan_tidak_berulang(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans();

        $a = $this->buat($menu)->json('data.kode');
        $b = $this->buat($menu)->json('data.kode');

        $this->assertNotSame($a, $b, 'Dua pesanan tidak boleh bernomor sama.');
        $this->assertDatabaseCount('pesanan', 2);
    }

    public function test_b5_server_key_dikirim_sebagai_basic_auth(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans();

        $this->buat($menu);

        Http::assertSent(fn ($req) =>
            str_contains($req->url(), '/transactions')
            && $req->hasHeader('Authorization',
                'Basic '.base64_encode('SB-Mid-server-UJI:')));
    }

    public function test_b6_status_settlement_menjadi_lunas(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans('settlement');
        $kode = $this->buat($menu)->json('data.kode');

        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)
            ->assertOk()
            ->assertJsonPath('data.status', 'lunas');
    }

    public function test_b7_status_expire_menjadi_expired(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans('expire');
        $kode = $this->buat($menu)->json('data.kode');

        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)
            ->assertJsonPath('data.status', 'expired');
    }

    public function test_b7b_status_deny_menjadi_gagal(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans('deny');
        $kode = $this->buat($menu)->json('data.kode');

        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)
            ->assertJsonPath('data.status', 'gagal');
    }

    public function test_b8_membaca_status_bersifat_idempoten(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans('settlement');
        $kode = $this->buat($menu)->json('data.kode');

        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)->assertOk();
        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)->assertOk();
        $this->getJson("/api/pesanan/{$kode}", self::KUNCI)->assertOk();

        $this->assertDatabaseCount('pesanan', 1);
    }

    public function test_b9_pesanan_tidak_ada_dijawab_404(): void
    {
        $this->fakeMidtrans();

        $this->getJson('/api/pesanan/TIDAK-ADA-001', self::KUNCI)
            ->assertStatus(404);
    }

    public function test_b10_endpoint_pesanan_tetap_dijaga_kunci_api(): void
    {
        $menu = $this->menu();
        $this->fakeMidtrans();

        $this->postJson('/api/pesanan',
            ['menu_id' => $menu->id, 'qty' => 1])->assertStatus(401);
        $this->getJson('/api/pesanan/APA-SAJA')->assertStatus(401);
    }
}
