<?php

namespace Tests\Feature\Tp07;

use App\Broker\PenerbitUji;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * TP 07 · Bagian C — Menerbitkan Tepat Sekali (35 poin)
 *
 * Dijalankan DI DALAM folder backend:
 *     php artisan test --filter=Tp073Terbit
 *
 * RabbitMQ TIDAK dinyalakan. `BROKER_DRIVER=uji` di backend/phpunit.xml
 * membuat Laravel memilih PenerbitUji, yang hanya menampung pesan di
 * memori. Panggilan Midtrans dipalsukan dengan Http::fake().
 */
class Tp073TerbitTest extends TestCase
{
    use RefreshDatabase;

    private const KUNCI = ['X-API-KEY' => 'rahasia-kantin-fit'];

    protected function setUp(): void
    {
        parent::setUp();
        PenerbitUji::$terkirim = [];
        config([
            'services.api.key'             => 'rahasia-kantin-fit',
            'services.midtrans.server_key' => 'SB-Mid-server-UJI',
            'services.midtrans.snap_url'   => 'https://snap-palsu.test/snap/v1',
            'services.midtrans.api_url'    => 'https://api-palsu.test',
            'services.midtrans.prefix'     => 'KF-UJI',
        ]);
    }

    private function pesanan(string $status = 'settlement'): string
    {
        $kategori = Category::create(['name' => 'Makanan']);
        $menu = Menu::create([
            'category_id' => $kategori->id,
            'name'        => 'Nasi Goreng',
            'price'       => 18000,
            'stock'       => 10,
        ]);

        Http::fake([
            '*/transactions' => Http::response(['token' => 'tok-abc-123'], 201),
            '*/status'       => Http::response(
                ['transaction_status' => $status, 'fraud_status' => 'accept'], 200),
            '*'              => Http::response([], 200),
        ]);

        return $this->postJson('/api/pesanan',
            ['menu_id' => $menu->id, 'qty' => 2], self::KUNCI)
            ->json('data.kode');
    }

    public function test_c1_pesanan_lunas_menerbitkan_satu_pesan(): void
    {
        $kode = $this->pesanan('settlement');

        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $this->assertCount(1, PenerbitUji::$terkirim);
        $this->assertSame('PesananDibayar',
            PenerbitUji::$terkirim[0]['event']);
        $this->assertSame($kode, PenerbitUji::$terkirim[0]['data']['kode']);
    }

    public function test_c2_isi_pesannya_cocok_dengan_baris_antriannya(): void
    {
        $kode = $this->pesanan('settlement');
        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $data = PenerbitUji::$terkirim[0]['data'];

        $this->assertSame('Nasi Goreng', $data['menu']);
        $this->assertSame(2, $data['qty']);
        $this->assertSame(36000, $data['total']);
        $this->assertGreaterThan(0, $data['menu_id']);
    }

    public function test_c3_dibuka_tiga_kali_tetap_satu_pesan(): void
    {
        $kode = $this->pesanan('settlement');

        for ($i = 0; $i < 3; $i++) {
            $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();
        }

        $this->assertCount(1, PenerbitUji::$terkirim,
            'Penjaga wasRecentlyCreated belum dipakai.');
    }

    public function test_c4_pesanan_yang_belum_lunas_tidak_menerbitkan_apa_pun(): void
    {
        $kode = $this->pesanan('pending');

        $this->getJson('/api/pesanan/'.$kode, self::KUNCI)->assertOk();

        $this->assertCount(0, PenerbitUji::$terkirim);
    }
}
