<?php

namespace Tests\Feature\Tp04;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Bagian D — Challenge: peran manajer (10 poin).
 *
 * Manajer boleh menambah dan mengubah menu, tetapi tidak boleh menghapus.
 * Admin tetap boleh melakukan semuanya.
 *
 *   php artisan test --filter=Tp044Manajer
 */
class Tp044ManajerTest extends TestCase
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

    private function menu(): Menu
    {
        return Menu::create([
            'category_id' => Category::firstOrCreate(['name' => 'Makanan'])->id,
            'name'        => 'Nasi Goreng',
            'price'       => 15000,
            'stock'       => 10,
            'note'        => null,
        ]);
    }

    /** Ability delete-menu harus dipisahkan dari manage-menu. */
    public function test_ability_delete_menu_terdaftar(): void
    {
        $this->assertTrue(
            Gate::has('delete-menu'),
            'Pisahkan aturan menghapus menjadi ability tersendiri, misalnya "delete-menu".'
        );
    }

    /** Manajer boleh mengubah menu. */
    public function test_manajer_boleh_mengubah_menu(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('manajer'))
            ->get('/menu/' . $menu->id . '/edit')
            ->assertOk();

        $this->actingAs($this->user('manajer'))
            ->put('/menu/' . $menu->id, [
                'category_id' => $menu->category_id,
                'name'        => 'Nasi Goreng Manajer',
                'price'       => 17000,
                'stock'       => 5,
                'note'        => null,
            ])
            ->assertRedirect(route('menu.index'));

        $this->assertDatabaseHas('menus', ['name' => 'Nasi Goreng Manajer']);
    }

    /** Manajer tidak boleh menghapus menu. */
    public function test_manajer_tidak_boleh_menghapus_menu(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('manajer'))
            ->delete('/menu/' . $menu->id)
            ->assertForbidden();

        $this->assertDatabaseHas('menus', ['id' => $menu->id]);
    }

    /** Tombol Hapus tidak digambar untuk manajer, tombol Ubah tetap ada. */
    public function test_tombol_hapus_tidak_tampil_untuk_manajer(): void
    {
        $this->menu();

        $this->actingAs($this->user('manajer'))
            ->get('/menu')
            ->assertOk()
            ->assertSee('Tambah Menu')
            ->assertDontSee('tombol-hapus', false);
    }

    /** Admin tetap boleh menghapus. */
    public function test_admin_tetap_boleh_menghapus(): void
    {
        $menu = $this->menu();

        $this->actingAs($this->user('admin'))
            ->delete('/menu/' . $menu->id)
            ->assertRedirect(route('menu.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }
}
