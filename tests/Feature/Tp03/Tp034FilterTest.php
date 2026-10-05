<?php

namespace Tests\Feature\Tp03;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Bagian D — Challenge: filter menu per kategori (10 poin).
 *
 * Yang diperiksa: tanpa parameter seluruh menu tampil, dengan ?category=<id>
 * hanya kategori itu yang tampil, dan parameter ngawur tidak membuat error.
 *
 *   php artisan test --filter=Tp034Filter
 */
class Tp034FilterTest extends TestCase
{
    use RefreshDatabase;

    private array $id = [];

    private function siapkanData(): void
    {
        $makanan = \App\Models\Category::create(['name' => 'Makanan']);
        $minuman = \App\Models\Category::create(['name' => 'Minuman']);

        $this->id = ['makanan' => $makanan->id, 'minuman' => $minuman->id];

        \App\Models\Menu::create([
            'category_id' => $makanan->id, 'name' => 'Nasi Goreng Spesial',
            'price' => 18000, 'stock' => 12, 'note' => null,
        ]);

        \App\Models\Menu::create([
            'category_id' => $minuman->id, 'name' => 'Es Teh Manis',
            'price' => 5000, 'stock' => 30, 'note' => null,
        ]);
    }

    /** Tanpa parameter, seluruh menu harus tampil. */
    public function test_tanpa_parameter_semua_menu_tampil(): void
    {
        $this->siapkanData();

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Nasi Goreng Spesial')
            ->assertSee('Es Teh Manis');
    }

    /** Dengan parameter kategori, hanya menu kategori itu yang tampil. */
    public function test_filter_kategori_menyaring_daftar(): void
    {
        $this->siapkanData();

        $this->get('/menu?category=' . $this->id['minuman'])
            ->assertOk()
            ->assertSee('Es Teh Manis')
            ->assertDontSee('Nasi Goreng Spesial');

        $this->get('/menu?category=' . $this->id['makanan'])
            ->assertOk()
            ->assertSee('Nasi Goreng Spesial')
            ->assertDontSee('Es Teh Manis');
    }

    /** Halaman daftar menyediakan pilihan kategori untuk disaring. */
    public function test_daftar_menu_memuat_pilihan_kategori(): void
    {
        $this->siapkanData();

        $this->get('/menu')
            ->assertOk()
            ->assertSee('name="category"', false)
            ->assertSee('Makanan')
            ->assertSee('Minuman');
    }

    /** Parameter yang tidak masuk akal tidak boleh membuat halaman error. */
    public function test_parameter_ngawur_tidak_membuat_error(): void
    {
        $this->siapkanData();

        $this->get('/menu?category=999999')->assertOk();
        $this->get('/menu?category=abc')->assertOk();
        $this->get('/menu?category=')->assertOk()->assertSee('Nasi Goreng Spesial');
    }
}
