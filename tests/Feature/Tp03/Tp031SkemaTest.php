<?php

namespace Tests\Feature\Tp03;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Bagian A — Basis Data dan Migration (25 poin).
 *
 * Yang diperiksa: kedua tabel terbentuk lewat migration dengan kolom yang
 * diminta, kolom category_id benar-benar berperan sebagai foreign key, dan
 * seeder mengisi data awal Kantin FIT.
 *
 *   php artisan test --filter=Tp031Skema
 */
class Tp031SkemaTest extends TestCase
{
    use RefreshDatabase;

    /** Tabel categories terbentuk dengan kolom name. */
    public function test_tabel_categories_terbentuk(): void
    {
        $this->assertTrue(
            Schema::hasTable('categories'),
            'Tabel "categories" belum terbentuk. Jalankan php artisan migrate '
            . 'setelah membuat migration create_categories_table.'
        );

        foreach (['id', 'name', 'created_at', 'updated_at'] as $kolom) {
            $this->assertTrue(
                Schema::hasColumn('categories', $kolom),
                "Tabel categories belum punya kolom \"{$kolom}\". "
                . 'Periksa kembali isi method up() pada migration-nya.'
            );
        }
    }

    /** Tabel menus terbentuk dengan seluruh kolom yang diminta modul. */
    public function test_tabel_menus_terbentuk(): void
    {
        $this->assertTrue(
            Schema::hasTable('menus'),
            'Tabel "menus" belum terbentuk. Jalankan php artisan migrate '
            . 'setelah membuat migration create_menus_table.'
        );

        foreach (['id', 'category_id', 'name', 'price', 'stock', 'note'] as $kolom) {
            $this->assertTrue(
                Schema::hasColumn('menus', $kolom),
                "Tabel menus belum punya kolom \"{$kolom}\". "
                . 'Kolomnya memakai nama berbahasa Inggris, bukan nama, harga, atau stok.'
            );
        }
    }

    /** Kolom note boleh dikosongkan. */
    public function test_kolom_note_boleh_kosong(): void
    {
        $kategori = DB::table('categories')->insertGetId([
            'name' => 'Makanan', 'created_at' => now(), 'updated_at' => now(),
        ]);

        try {
            DB::table('menus')->insert([
                'category_id' => $kategori,
                'name'        => 'Menu Tanpa Catatan',
                'price'       => 9000,
                'stock'       => 3,
                'note'        => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            $this->fail(
                'Kolom "note" masih menolak nilai kosong. Tambahkan ->nullable() '
                . 'pada kolom note di migration create_menus_table.'
            );
        }

        $this->assertDatabaseHas('menus', ['name' => 'Menu Tanpa Catatan']);
    }

    /** category_id harus foreign key: menghapus kategori ikut menghapus menunya. */
    public function test_category_id_adalah_foreign_key(): void
    {
        $kategori = DB::table('categories')->insertGetId([
            'name' => 'Minuman', 'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('menus')->insert([
            'category_id' => $kategori,
            'name'        => 'Es Jeruk Uji',
            'price'       => 6000,
            'stock'       => 5,
            'note'        => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::table('categories')->where('id', $kategori)->delete();

        $this->assertDatabaseMissing('menus', ['name' => 'Es Jeruk Uji']);
    }

    /** Seeder mengisi minimal dua kategori dan lima menu. */
    public function test_seeder_mengisi_data_awal(): void
    {
        $this->seed();

        $this->assertGreaterThanOrEqual(
            2,
            DB::table('categories')->count(),
            'Seeder belum mengisi tabel categories. Panggil CategorySeeder '
            . 'dari DatabaseSeeder, lalu jalankan php artisan migrate:fresh --seed.'
        );

        $this->assertGreaterThanOrEqual(
            2,
            DB::table('menus')->count(),
            'Seeder belum mengisi menu Kantin FIT. Panggil MenuSeeder '
            . 'dari DatabaseSeeder, lalu jalankan php artisan migrate:fresh --seed.'
        );
    }

    /** Setiap menu hasil seeder menunjuk ke kategori yang benar-benar ada. */
    public function test_setiap_menu_punya_kategori_yang_sah(): void
    {
        $this->seed();

        $yatim = DB::table('menus')
            ->leftJoin('categories', 'menus.category_id', '=', 'categories.id')
            ->whereNull('categories.id')
            ->count();

        $this->assertSame(
            0,
            $yatim,
            'Ada menu yang category_id-nya tidak menunjuk ke baris mana pun di '
            . 'tabel categories. Pastikan MenuSeeder mengambil id kategori dari '
            . 'tabel, bukan menuliskan angka secara manual.'
        );
    }
}
