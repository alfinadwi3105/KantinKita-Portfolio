<?php

namespace Tests\Feature\Tp04;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Bagian C — Hak Akses Berdasarkan Peran (35 poin).
 *
 * Yang diperiksa: ability manage-menu terdaftar, kasir ditolak dengan 403
 * pada semua aksi pengelolaan menu, admin tetap bisa, dan tombolnya tidak
 * digambar untuk kasir.
 *
 *   php artisan test --filter=Tp043Akses
 */
class Tp043AksesTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::firstOrCreate(
            ['email' => $role . '@uji.test'],
            [
                'name'     => ucfirst($role) . ' Uji',
                'password' => 'rahasia123',
                'role'     => $role,
            ]
        );
    }

    private function kategori(string $nama = 'Makanan'): Category
    {
        return Category::firstOrCreate(['name' => $nama]);
    }

    private function menu(): Menu
    {
        return Menu::create([
            'category_id' => $this->kategori()->id,
            'name'        => 'Nasi Goreng',
            'price'       => 15000,
            'stock'       => 10,
            'note'        => null,
        ]);
    }

    private function isian(): array
    {
        return [
            'category_id' => $this->kategori()->id,
            'name'        => 'Mie Ayam',
            'price'       => 13000,
            'stock'       => 8,
            'note'        => null,
        ];
    }

    /** Ability manage-menu harus didaftarkan lewat Gate::define(). */
    public function test_ability_manage_menu_terdaftar(): void
    {
        $this->assertTrue(
            Gate::has('manage-menu'),
            'Daftarkan Gate::define("manage-menu", ...) di AppServiceProvider::boot().'
        );
    }

    /** Kasir tetap boleh melihat daftar dan detail menu. */
    public function test_kasir_boleh_melihat_menu(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('kasir'))->get('/menu')->assertOk();
        $this->actingAs($this->user('kasir'))->get('/menu/' . $menu->id)->assertOk();
    }

    /** Kasir ditolak saat membuka halaman tambah menu. */
    public function test_kasir_ditolak_di_halaman_tambah(): void
    {
        $this->actingAs($this->user('kasir'))
            ->get('/menu/create')
            ->assertForbidden();
    }

    /** Kasir ditolak saat mengirim data menu baru. */
    public function test_kasir_ditolak_saat_menyimpan(): void
    {
        $this->actingAs($this->user('kasir'))
            ->post('/menu', $this->isian())
            ->assertForbidden();

        $this->assertDatabaseMissing('menus', ['name' => 'Mie Ayam']);
    }

    /** Kasir ditolak saat membuka halaman ubah. */
    public function test_kasir_ditolak_di_halaman_ubah(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('kasir'))
            ->get('/menu/' . $menu->id . '/edit')
            ->assertForbidden();
    }

    /** Kasir ditolak saat menghapus menu. */
    public function test_kasir_ditolak_saat_menghapus(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('kasir'))
            ->delete('/menu/' . $menu->id)
            ->assertForbidden();

        $this->assertDatabaseHas('menus', ['id' => $menu->id]);
    }

    /** Admin boleh menambah menu. */
    public function test_admin_boleh_menambah_menu(): void
    {
        $this->actingAs($this->user('admin'))
            ->get('/menu/create')
            ->assertOk();

        $this->actingAs($this->user('admin'))
            ->post('/menu', $this->isian())
            ->assertRedirect(route('menu.index'));

        $this->assertDatabaseHas('menus', ['name' => 'Mie Ayam']);
    }

    /** Admin boleh mengubah dan menghapus menu. */
    public function test_admin_boleh_mengubah_dan_menghapus(): void
    {
        $menu  = $this->menu();
        $admin = $this->user('admin');

        $this->actingAs($admin)
            ->get('/menu/' . $menu->id . '/edit')
            ->assertOk();

        $this->actingAs($admin)
            ->put('/menu/' . $menu->id, array_merge($this->isian(), ['name' => 'Nasi Goreng Pedas']))
            ->assertRedirect(route('menu.index'));

        $this->assertDatabaseHas('menus', ['name' => 'Nasi Goreng Pedas']);

        $this->actingAs($admin)
            ->delete('/menu/' . $menu->id)
            ->assertRedirect(route('menu.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    /** Tombol pengelolaan tidak digambar untuk kasir. */
    public function test_tombol_tidak_tampil_untuk_kasir(): void
    {
        $this->menu();

        $halaman = $this->actingAs($this->user('kasir'))->get('/menu');

        $halaman->assertOk()
            ->assertDontSee('Tambah Menu')
            ->assertDontSee('tombol-hapus', false);
    }

    /** Tombol pengelolaan tetap digambar untuk admin. */
    public function test_tombol_tampil_untuk_admin(): void
    {
        $this->menu();

        $this->actingAs($this->user('admin'))
            ->get('/menu')
            ->assertOk()
            ->assertSee('Tambah Menu')
            ->assertSee('tombol-hapus', false);
    }
}