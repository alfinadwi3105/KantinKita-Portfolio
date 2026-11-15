<?php

namespace Tests\Feature\Tp07;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * TP 07 · Bagian A — Antrian di Backend dan Halaman /antrian (25 poin)
 *
 * Dijalankan DI DALAM folder backend:
 *     php artisan test --filter=Tp071Antrian
 *
 * Yang diperiksa hanya pokoknya: tabelnya ada, barisnya lahir tepat sekali
 * untuk pesanan yang lunas, dan endpointnya dijaga kunci. Rapi tidaknya
 * halaman /antrian di :8000 dinilai asisten saat responsi, bukan di sini.
 *
 * Panggilan ke Midtrans dipalsukan, jadi tes ini tidak butuh kunci sandbox
 * maupun koneksi internet.
 */
class Tp071AntrianTest extends TestCase
{
    use RefreshDatabase;

    private const KUNCI = ['X-API-KEY' => 'rahasia-kantin-fit'];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.api.key'             => 'rahasia-kantin-fit',
            'services.midtrans.server_key' => 'SB-Mid-server-UJI',
            'services.midtrans.snap_url'   => 'https://snap-palsu.test/snap/v1',
            'services.midtrans.api_url'    => 'https://api-palsu.test',
            'services.midtrans.prefix'     => 'KF-UJI',
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

    /** Membuat satu pesanan lalu mengembalikan kodenya. */
    private function pesanan(string $status = 'settlement'): string
    {
        $menu = $this->menu();
        $this->fakeMidtrans($status);

        return $this->postJson('/api/pesanan',
            ['menu_id' => $menu->id, 'qty' => 2], self::KUNCI)
            ->json('data.kode');
    }

    public function test_a1_tabel_antrian_ada_dengan_kolom_yang_dibutuhkan(): void
    {
        $this->assertTrue(Schema::hasTable('antrian'),
            'Tabel antrian belum ada. Nama tabelnya "antrian", bukan "antrians".');

        $this->assertTrue(Schema::hasColumns('antrian', [
            'kode', 'menu_id', 'menu', 'qty', 'total', 'status', 'dipesan_pada',
        ]), 'Ada kolom yang belum dibuat di tabel antrian.');
    }

    public function test_a2_pesanan_lunas_menghasilkan_satu_baris_antrian(): void
    {
        $kode = $this->pesanan('settlement');

        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $this->assertDatabaseCount('antrian', 1);
        $this->assertDatabaseHas('antrian', [
            'kode'   => $kode,
            'status' => 'diproses',
            'qty'    => 2,
        ]);
    }

    public function test_a3_dibuka_tiga_kali_tetap_satu_baris(): void
    {
        $kode = $this->pesanan('settlement');

        for ($i = 0; $i < 3; $i++) {
            $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();
        }

        $this->assertDatabaseCount('antrian', 1);
    }

    public function test_a4_pesanan_yang_belum_lunas_tidak_masuk_antrian(): void
    {
        $kode = $this->pesanan('pending');

        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $this->assertDatabaseCount('antrian', 0);
    }

    public function test_a5_endpoint_antrian_dijaga_kunci_dan_menampilkan_isinya(): void
    {
        $kode = $this->pesanan('settlement');
        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $this->getJson('/api/antrian')->assertStatus(401);

        $res = $this->getJson('/api/antrian', self::KUNCI);

        $res->assertOk();
        $this->assertCount(1, $res->json());
        $this->assertSame($kode, $res->json('0.kode'));
    }
}
