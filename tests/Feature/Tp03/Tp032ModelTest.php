<?php

namespace Tests\Feature\Tp03;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Bagian B — Model, Relasi, dan Eager Loading (25 poin).
 *
 * Yang diperiksa: kedua model ada dengan $fillable yang benar, relasi dua arah
 * berfungsi, halaman daftar menu mengambil datanya dari tabel lengkap dengan
 * nama kategori, dan daftarnya memakai eager loading sehingga tidak N+1.
 *
 *   php artisan test --filter=Tp032Model
 */
class Tp032ModelTest extends TestCase
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

    /** Kedua model harus ada di namespace App\Models. */
    public function test_model_category_dan_menu_ada(): void
    {
        foreach (['App\Models\Category', 'App\Models\Menu'] as $kelas) {
            $this->assertTrue(
                class_exists($kelas),
                "Class {$kelas} belum ada. Buat dengan php artisan make:model "
                . class_basename($kelas) . ' — berkasnya akan tersimpan di app/Models.'
            );
        }
    }

    /** $fillable harus memuat kolom yang diisi lewat form. */
    public function test_fillable_model_menu_lengkap(): void
    {
        $fillable = (new \App\Models\Menu)->getFillable();

        foreach (['category_id', 'name', 'price', 'stock', 'note'] as $kolom) {
            $this->assertContains(
                $kolom,
                $fillable,
                "Kolom \"{$kolom}\" belum terdaftar di \$fillable pada App\\Models\\Menu. "
                . 'Tanpa itu Menu::create() akan melempar MassAssignmentException.'
            );
        }
    }

    /** Category::menus() harus mengembalikan relasi hasMany. */
    public function test_relasi_category_punya_banyak_menu(): void
    {
        $kategori = $this->kategori('Minuman');

        $this->assertTrue(
            method_exists($kategori, 'menus'),
            'Method menus() belum ada di App\Models\Category.'
        );

        $this->assertInstanceOf(
            HasMany::class,
            $kategori->menus(),
            'Method menus() pada Category harus mengembalikan $this->hasMany(Menu::class).'
        );

        \App\Models\Menu::create([
            'category_id' => $kategori->id,
            'name' => 'Es Teh Manis', 'price' => 5000, 'stock' => 30, 'note' => null,
        ]);

        $this->assertCount(
            1,
            $kategori->fresh()->menus,
            '$category->menus belum mengembalikan menu yang terhubung dengannya.'
        );
    }

    /** Menu::category() harus mengembalikan relasi belongsTo. */
    public function test_relasi_menu_milik_satu_category(): void
    {
        $menu = $this->menu();

        $this->assertTrue(
            method_exists($menu, 'category'),
            'Method category() belum ada di App\Models\Menu.'
        );

        $this->assertInstanceOf(
            BelongsTo::class,
            $menu->category(),
            'Method category() pada Menu harus mengembalikan $this->belongsTo(Category::class).'
        );

        $this->assertNotNull(
            $menu->category,
            '$menu->category masih kosong. Periksa apakah kolom category_id sudah terisi.'
        );
    }

    /** Halaman daftar menu mengambil datanya dari tabel, bukan dari array. */
    public function test_daftar_menu_menampilkan_data_dari_tabel(): void
    {
        $kategori = $this->kategori('Minuman');
        $this->menu(['category_id' => $kategori->id, 'name' => 'Kopi Susu Gula Aren']);

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Kopi Susu Gula Aren')
            ->assertSee('Minuman');
    }

    /** Halaman detail menampilkan satu menu beserta nama kategorinya. */
    public function test_halaman_detail_menampilkan_nama_kategori(): void
    {
        $kategori = $this->kategori('Makanan');
        $menu = $this->menu(['category_id' => $kategori->id, 'name' => 'Ayam Geprek']);

        $this->get('/menu/' . $menu->id)
            ->assertOk()
            ->assertSee('Ayam Geprek')
            ->assertSee('Makanan');
    }

    /** Id yang tidak ada harus menghasilkan 404, bukan 500. */
    public function test_menu_tidak_ada_menghasilkan_404(): void
    {
        $this->get('/menu/999999')->assertNotFound();
    }

    /** Daftar menu wajib memakai with() supaya tidak N+1. */
    public function test_daftar_menu_memakai_eager_loading(): void
    {
        $makanan = $this->kategori('Makanan');
        $minuman = $this->kategori('Minuman');

        foreach (range(1, 20) as $nomor) {
            \App\Models\Menu::create([
                'category_id' => $nomor % 2 === 0 ? $makanan->id : $minuman->id,
                'name'  => 'Menu Uji ' . $nomor,
                'price' => 10000 + $nomor,
                'stock' => $nomor,
                'note'  => null,
            ]);
        }

        $jumlah = 0;
        DB::listen(function () use (&$jumlah) {
            $jumlah++;
        });

        $this->get('/menu')->assertOk();

        $this->assertLessThanOrEqual(
            4,
            $jumlah,
            "Membuka /menu dengan 20 baris data menjalankan {$jumlah} query. "
            . 'Itu tanda relasinya di-load satu per satu (masalah N+1). '
            . "Pakai Menu::with('category')->get() di method index()."
        );
    }
}
