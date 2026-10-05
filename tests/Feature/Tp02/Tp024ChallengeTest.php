<?php

namespace Tests\Feature\Tp02;

use Tests\TestCase;

/**
 * Bagian D — Challenge: isian stok (10 poin).
 *
 * Tiga hal dikerjakan sendiri tanpa dituntun langkah demi langkah:
 * menambah isian baru pada form, menulis aturan validasinya, dan
 * menampilkan penanda "Habis" lewat percabangan di Blade.
 *
 *   php artisan test --filter=Tp024Challenge
 */
class Tp024ChallengeTest extends TestCase
{
    private function dataSah(array $ganti = []): array
    {
        return array_merge([
            'nama'     => 'Roti Bakar Cokelat',
            'kategori' => 'makanan',
            'harga'    => 14000,
            'stok'     => 7,
            'catatan'  => null,
        ], $ganti);
    }

    /** Form harus memuat isian stok. */
    public function test_form_memuat_isian_stok(): void
    {
        $this->get(route('menu.create'))
            ->assertStatus(200)
            ->assertSee('name="stok"', false);
    }

    /** Stok wajib diisi. */
    public function test_stok_wajib_diisi(): void
    {
        $data = $this->dataSah();
        unset($data['stok']);

        $this->from(route('menu.create'))
            ->post(route('menu.store'), $data)
            ->assertSessionHasErrors('stok');
    }

    /** Stok tidak boleh bernilai negatif. */
    public function test_stok_negatif_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['stok' => -3]))
            ->assertSessionHasErrors('stok');
    }

    /** Stok harus bilangan bulat, bukan teks atau pecahan. */
    public function test_stok_bukan_bilangan_bulat_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['stok' => 'banyak']))
            ->assertSessionHasErrors('stok');

        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['stok' => 2.5]))
            ->assertSessionHasErrors('stok');
    }

    /** Menu yang stoknya nol harus ditandai Habis di daftar. */
    public function test_menu_stok_nol_ditandai_habis(): void
    {
        $this->post(route('menu.store'), $this->dataSah([
            'nama' => 'Pisang Goreng Madu',
            'stok' => 0,
        ]));

        $this->get('/menu')
            ->assertStatus(200)
            ->assertSee('Pisang Goreng Madu')
            ->assertSee('Habis');
    }

    /** Menu yang masih ada stoknya tidak boleh ikut ditandai Habis. */
    public function test_menu_berstok_tidak_ditandai_habis(): void
    {
        $sebelum = substr_count($this->get('/menu')->getContent(), 'Habis');

        $this->post(route('menu.store'), $this->dataSah([
            'nama' => 'Teh Tarik Dingin',
            'stok' => 25,
        ]));

        $sesudah = substr_count($this->get('/menu')->getContent(), 'Habis');

        $this->assertSame(
            $sebelum,
            $sesudah,
            'Menu yang stoknya masih ada ikut ditandai "Habis". '
            . 'Periksa kembali syarat pada percabangan @if di partial kartu menu.'
        );
    }
}
