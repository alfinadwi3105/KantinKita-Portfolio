<?php

namespace Tests\Feature\Tp03;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Bagian C — CRUD Lengkap (40 poin).
 *
 * Yang diperiksa: tujuh rute resource terdaftar, data benar-benar masuk,
 * berubah, dan terhapus dari tabel, serta validasi menolak kategori palsu.
 *
 *   php artisan test --filter=Tp033Crud
 */
class Tp033CrudTest extends TestCase
{
    use RefreshDatabase;

    private function kategori(string $nama = 'Makanan')
    {
        return \App\Models\Category::firstOrCreate(['name' => $nama]);
    }

    private function menu(array $isi = [])
    {
        $isi['category_id'] = $isi['category_id'] ?? $this->kategori()->id;

        return \App\Models\Menu::create(array_merge([
            'name'  => 'Nasi Goreng Spesial',
            'price' => 18000,
            'stock' => 12,
            'note'  => null,
        ], $isi));
    }

    private function isian(array $ubah = []): array
    {
        return array_merge([
            'category_id' => $this->kategori('Minuman')->id,
            'name'        => 'Es Kopi Susu',
            'price'       => 14000,
            'stock'       => 9,
            'note'        => 'Dingin',
        ], $ubah);
    }

    /** Ketujuh rute resource harus terdaftar dengan verb yang benar. */
    public function test_tujuh_rute_resource_terdaftar(): void
    {
        $harapan = [
            'menu.index'   => ['GET',    '/^menu$/'],
            'menu.create'  => ['GET',    '#^menu/create$#'],
            'menu.store'   => ['POST',   '/^menu$/'],
            'menu.show'    => ['GET',    '#^menu/\{\w+\}$#'],
            'menu.edit'    => ['GET',    '#^menu/\{\w+\}/edit$#'],
            'menu.update'  => ['PUT',    '#^menu/\{\w+\}$#'],
            'menu.destroy' => ['DELETE', '#^menu/\{\w+\}$#'],
        ];

        foreach ($harapan as $nama => [$verb, $pola]) {
            $rute = Route::getRoutes()->getByName($nama);

            $this->assertNotNull(
                $rute,
                "Route \"{$nama}\" belum terdaftar. Hapus ->only([...]) dari "
                . 'Route::resource supaya ketujuh rutenya terbentuk.'
            );

            $this->assertMatchesRegularExpression(
                $pola, $rute->uri(),
                "Alamat route \"{$nama}\" belum sesuai. Yang terbaca: /{$rute->uri()}"
            );

            $this->assertContains(
                $verb, $rute->methods(),
                "Route \"{$nama}\" harus memakai verb {$verb}."
            );
        }
    }

    /** Halaman form tambah memuat dropdown kategori dari tabel. */
    public function test_form_tambah_memuat_pilihan_kategori(): void
    {
        $this->kategori('Minuman');

        $this->get('/menu/create')
            ->assertOk()
            ->assertSee('name="category_id"', false)
            ->assertSee('Minuman');
    }

    /** Data yang sah harus benar-benar tersimpan di tabel. */
    public function test_menu_baru_tersimpan_ke_tabel(): void
    {
        $isian = $this->isian();

        $this->post('/menu', $isian)->assertRedirect(route('menu.index'));

        $this->assertDatabaseHas('menus', [
            'name'        => 'Es Kopi Susu',
            'price'       => 14000,
            'stock'       => 9,
            'category_id' => $isian['category_id'],
        ]);
    }

    /** Kategori yang tidak terdaftar harus ditolak validasi. */
    public function test_kategori_palsu_ditolak(): void
    {
        $this->post('/menu', $this->isian(['category_id' => 999999]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseMissing('menus', ['name' => 'Es Kopi Susu']);
    }

    /** Isian kosong tidak boleh menambah baris. */
    public function test_isian_kosong_tidak_menambah_baris(): void
    {
        $this->post('/menu', [])
            ->assertSessionHasErrors(['category_id', 'name', 'price', 'stock']);

        $this->assertSame(0, \App\Models\Menu::count());
    }

    /** Halaman edit menampilkan nilai lama di dalam form. */
    public function test_halaman_edit_terisi_nilai_lama(): void
    {
        $menu = $this->menu(['name' => 'Mie Ayam Bakso', 'price' => 15000]);

        $this->get('/menu/' . $menu->id . '/edit')
            ->assertOk()
            ->assertSee('Mie Ayam Bakso')
            ->assertSee('15000');
    }

    /** Perubahan harus tersimpan ke baris yang tepat. */
    public function test_menu_bisa_diperbarui(): void
    {
        $menu = $this->menu(['name' => 'Mie Ayam Bakso', 'price' => 15000, 'stock' => 4]);

        $this->put('/menu/' . $menu->id, [
            'category_id' => $menu->category_id,
            'name'        => 'Mie Ayam Bakso Jumbo',
            'price'       => 19000,
            'stock'       => 6,
            'note'        => 'Porsi besar',
        ])->assertRedirect(route('menu.index'));

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id, 'name' => 'Mie Ayam Bakso Jumbo', 'price' => 19000,
        ]);

        $this->assertDatabaseMissing('menus', ['name' => 'Mie Ayam Bakso']);
    }

    /** Perubahan yang gagal validasi tidak boleh ikut tersimpan. */
    public function test_perubahan_tidak_sah_ditolak(): void
    {
        $menu = $this->menu(['name' => 'Ayam Geprek', 'price' => 16000]);

        $this->put('/menu/' . $menu->id, [
            'category_id' => $menu->category_id,
            'name'        => '',
            'price'       => 500,
            'stock'       => -3,
        ])->assertSessionHasErrors(['name', 'price', 'stock']);

        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Ayam Geprek']);
    }

    /** Menu bisa dihapus dan barisnya benar-benar hilang. */
    public function test_menu_bisa_dihapus(): void
    {
        $menu = $this->menu(['name' => 'Menu Yang Dihapus']);

        $this->delete('/menu/' . $menu->id)->assertRedirect(route('menu.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    /** Daftar menu memuat tautan ubah dan tombol hapus. */
    public function test_daftar_menu_memuat_tombol_ubah_dan_hapus(): void
    {
        $menu = $this->menu(['name' => 'Kopi Susu Gula Aren']);

        $halaman = $this->get('/menu')->assertOk();

        $halaman->assertSee(route('menu.edit', $menu->id), false);
        $halaman->assertSee('DELETE', false);
    }

    /** Menghapus menu yang tidak ada menghasilkan 404, bukan 500. */
    public function test_hapus_menu_tidak_ada_menghasilkan_404(): void
    {
        $this->delete('/menu/999999')->assertNotFound();
    }
}
