<?php

namespace Tests\Feature\Tp02;

use Tests\TestCase;

/**
 * Bagian C — Form dan Validasi (35 poin).
 *
 * Ini kriteria berbobot terbesar karena di sinilah seluruh rantai dibuktikan:
 * form dengan @csrf, validasi di server, pesan kesalahan, isian lama yang
 * kembali, lalu redirect dan pesan sukses.
 *
 *   php artisan test --filter=Tp023Validasi
 */
class Tp023ValidasiTest extends TestCase
{
    /** Data yang pasti lolos seluruh aturan validasi. */
    private function dataSah(array $ganti = []): array
    {
        return array_merge([
            'nama'     => 'Soto Ayam Lamongan',
            'kategori' => 'makanan',
            'harga'    => 17000,
            'stok'     => 10,
            'catatan'  => 'Pakai koya',
        ], $ganti);
    }

    /** Halaman form harus tampil lengkap dengan token CSRF dan isiannya. */
    public function test_halaman_form_tampil_lengkap(): void
    {
        $respons = $this->get(route('menu.create'));

        $respons->assertStatus(200);
        $respons->assertViewIs('menu.create');

        $html = $respons->getContent();

        $this->assertStringContainsString(
            'name="_token"',
            $html,
            'Form belum memuat token CSRF. Tambahkan @csrf tepat di bawah tag <form>.'
        );

        $this->assertMatchesRegularExpression(
            '/<form[^>]*method\s*=\s*["\']POST["\']/i',
            $html,
            'Tag <form> harus memakai method="POST".'
        );

        $this->assertMatchesRegularExpression(
            '/<form[^>]*action\s*=\s*["\'][^"\']*\/menu["\']/i',
            $html,
            "Atribut action pada form harus menunjuk ke route('menu.store')."
        );

        foreach (['nama', 'kategori', 'harga'] as $isian) {
            $this->assertMatchesRegularExpression(
                '/name\s*=\s*["\']' . $isian . '["\']/',
                $html,
                "Form belum memuat isian bernama \"{$isian}\"."
            );
        }
    }

    /** Form kosong harus ditolak dan dikembalikan, bukan diterima. */
    public function test_form_kosong_ditolak(): void
    {
        $respons = $this->from(route('menu.create'))->post(route('menu.store'), []);

        $respons->assertStatus(302);
        $respons->assertRedirect(route('menu.create'));
        $respons->assertSessionHasErrors(['nama', 'kategori', 'harga', 'stok']);
    }

    /** Isian yang tadi diketik harus ikut dikembalikan ke form. */
    public function test_isian_lama_dikembalikan(): void
    {
        $this->from(route('menu.create'))->post(route('menu.store'), [
            'nama'     => 'Es Kopi Aren',
            'kategori' => '',
            'harga'    => '',
            'stok'     => '',
        ]);

        $this->assertSame(
            'Es Kopi Aren',
            session()->getOldInput('nama'),
            'Input lama tidak ikut dikembalikan. Pastikan value isian memakai old().'
        );

        $this->get(route('menu.create'))->assertSee('Es Kopi Aren');
    }

    /** Harga yang bukan angka harus ditolak. */
    public function test_harga_bukan_angka_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['harga' => 'mahal']))
            ->assertSessionHasErrors('harga');
    }

    /** Harga di bawah batas minimal harus ditolak. */
    public function test_harga_di_bawah_minimal_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['harga' => 500]))
            ->assertSessionHasErrors('harga');
    }

    /** Kategori di luar daftar yang sah harus ditolak. */
    public function test_kategori_di_luar_daftar_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['kategori' => 'dessert']))
            ->assertSessionHasErrors('kategori');
    }

    /** Nama yang terlalu panjang harus ditolak. */
    public function test_nama_terlalu_panjang_ditolak(): void
    {
        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['nama' => str_repeat('a', 61)]))
            ->assertSessionHasErrors('nama');
    }

    /** Menu tidak boleh bertambah kalau validasinya gagal. */
    public function test_menu_tidak_bertambah_saat_validasi_gagal(): void
    {
        $sebelum = count($this->get('/menu')->original->getData()['menu']);

        $this->from(route('menu.create'))
            ->post(route('menu.store'), $this->dataSah(['kategori' => 'dessert']));

        $sesudah = count($this->get('/menu')->original->getData()['menu']);

        $this->assertSame(
            $sebelum,
            $sesudah,
            'Menu bertambah padahal validasinya gagal. '
            . 'Pastikan penyimpanan dilakukan setelah validate(), bukan sebelumnya.'
        );
    }

    /** Data yang sah harus disimpan lalu dialihkan dengan pesan sukses. */
    public function test_data_sah_disimpan_dan_muncul_di_daftar(): void
    {
        $respons = $this->post(route('menu.store'), $this->dataSah());

        $respons->assertRedirect(route('menu.index'));
        $respons->assertSessionHas('status');
        $respons->assertSessionHasNoErrors();

        $this->get('/menu')
            ->assertStatus(200)
            ->assertSee('Soto Ayam Lamongan');
    }

    /** Setelah berhasil, pengguna mendarat di daftar menu dan melihat hasilnya. */
    public function test_pesan_sukses_dikirim_dan_halaman_daftar_tampil(): void
    {
        $this->post(route('menu.store'), $this->dataSah(['nama' => 'Nasi Uduk Komplit']))
            ->assertRedirect(route('menu.index'))
            ->assertSessionHas('status');

        $this->followingRedirects()
            ->post(route('menu.store'), $this->dataSah(['nama' => 'Lontong Sayur']))
            ->assertStatus(200)
            ->assertSee('Lontong Sayur');
    }

    /** Dua menu berbeda harus bisa ditambahkan berurutan. */
    public function test_dua_menu_berturut_turut_tersimpan(): void
    {
        $this->post(route('menu.store'), $this->dataSah(['nama' => 'Bakso Urat']));
        $this->post(route('menu.store'), $this->dataSah(['nama' => 'Es Jeruk Peras']));

        $this->get('/menu')
            ->assertSee('Bakso Urat')
            ->assertSee('Es Jeruk Peras');
    }

    /** Nama yang mengandung tag HTML harus tampil sebagai teks biasa. */
    public function test_nama_berisi_tag_ditampilkan_apa_adanya(): void
    {
        $this->post(route('menu.store'), $this->dataSah(['nama' => '<b>Bakso Beranak</b>']));

        $html = $this->get('/menu')->getContent();

        $this->assertStringContainsString(
            '&lt;b&gt;Bakso Beranak&lt;/b&gt;',
            $html,
            'Nama menu tidak ter-escape. Pastikan kamu memakai {{ }}, bukan {!! !!}.'
        );

        $this->assertStringNotContainsString(
            '<b>Bakso Beranak</b>',
            $html,
            'Tag HTML dari pengguna ikut dijalankan browser. Ini celah XSS.'
        );
    }
}
