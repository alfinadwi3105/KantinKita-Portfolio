<?php

namespace Tests\Feature\Tp02;

use Tests\TestCase;

/**
 * Bagian B — Template Tampilan (30 poin).
 *
 * Halaman bisa terlihat benar sempurna sambil seluruh tautannya ditulis
 * manual dan kerangka HTML-nya disalin ke mana-mana. Karena itu sebagian
 * pemeriksaan di sini membaca isi berkas Blade, bukan hanya hasil render.
 *
 *   php artisan test --filter=Tp022Tampilan
 */
class Tp022TampilanTest extends TestCase
{
    private function isiView(string $nama): string
    {
        $berkas = resource_path("views/{$nama}.blade.php");

        $this->assertFileExists($berkas, "Berkas resources/views/{$nama}.blade.php tidak ditemukan.");

        return file_get_contents($berkas);
    }

    /** Ketiga halaman menu harus menurunkan diri dari layout induk. */
    public function test_view_menu_memakai_layout_induk(): void
    {
        foreach (['menu/index', 'menu/show', 'menu/create'] as $view) {
            $this->assertMatchesRegularExpression(
                "/@extends\(\s*['\"]layouts\.app['\"]\s*\)/",
                $this->isiView($view),
                "Berkas {$view}.blade.php belum memuat @extends('layouts.app')."
            );
        }
    }

    /** Halaman anak tidak boleh menulis ulang kerangka HTML. */
    public function test_view_menu_tidak_menulis_kerangka_html(): void
    {
        foreach (['menu/index', 'menu/show', 'menu/create'] as $view) {
            $isi = $this->isiView($view);

            foreach (['<html', '<body', '<head'] as $tag) {
                $this->assertStringNotContainsString(
                    $tag,
                    $isi,
                    "Berkas {$view}.blade.php masih menulis tag {$tag}> sendiri. "
                    . 'Kerangka HTML cukup ditulis sekali di layout induk.'
                );
            }
        }
    }

    /** Navigasi di layout harus punya tautan ke daftar menu. */
    public function test_navigasi_memuat_tautan_menu(): void
    {
        $layout = $this->isiView('layouts/app');

        $this->assertMatchesRegularExpression(
            "/route\(\s*['\"]menu\.index['\"]/",
            $layout,
            "Navigasi di layout belum memuat tautan ke route('menu.index')."
        );

        foreach (['/', '/menu', '/tentang'] as $alamat) {
            $this->get($alamat)->assertStatus(200)->assertSee('Menu');
        }
    }

    /** Tautan tidak boleh ditulis manual — harus lewat helper route(). */
    public function test_tautan_tidak_ditulis_manual(): void
    {
        foreach ($this->semuaBerkasBlade() as $berkas) {
            $isi = file_get_contents($berkas);
            $nama = str_replace(resource_path('views') . '/', '', $berkas);

            $this->assertDoesNotMatchRegularExpression(
                '/href\s*=\s*["\']\/menu/',
                $isi,
                "Berkas {$nama} masih menulis alamat /menu langsung di href. "
                . "Ganti dengan {{ route('menu.index') }} atau nama rute lain yang sesuai."
            );
        }
    }

    /** Berkas gaya harus ada dan dipasang lewat helper asset(). */
    public function test_berkas_css_ada_dan_terpasang(): void
    {
        $berkas = public_path('css/app.css');

        $this->assertFileExists(
            $berkas,
            'Berkas public/css/app.css tidak ditemukan. '
            . 'Buat berkas gaya di public/css/app.css.'
        );

        $this->assertNotEmpty(
            trim(file_get_contents($berkas)),
            'Berkas public/css/app.css masih kosong.'
        );

        $this->assertMatchesRegularExpression(
            "/asset\(\s*['\"]css\/app\.css['\"]\s*\)/",
            $this->isiView('layouts/app'),
            "Layout induk belum memasang berkas gaya. "
            . "Tambahkan <link rel=\"stylesheet\" href=\"{{ asset('css/app.css') }}\"> di dalam <head>."
        );

        $this->get('/menu')->assertStatus(200)->assertSee('css/app.css', false);
    }

    /** Potongan kartu menu harus dipisah jadi partial dan benar-benar dipakai. */
    public function test_partial_kartu_menu_ada_dan_dipakai(): void
    {
        $this->assertFileExists(
            resource_path('views/menu/partials/kartu.blade.php'),
            'Berkas resources/views/menu/partials/kartu.blade.php tidak ditemukan.'
        );

        $this->assertMatchesRegularExpression(
            "/@include\(\s*['\"]menu\.partials\.kartu['\"]/",
            $this->isiView('menu/index'),
            "Halaman daftar menu belum memakai @include('menu.partials.kartu')."
        );
    }

    /** Komponen alert harus ada dan dipakai untuk menampilkan pesan. */
    public function test_komponen_alert_ada_dan_dipakai(): void
    {
        $this->assertFileExists(
            resource_path('views/components/alert.blade.php'),
            'Berkas resources/views/components/alert.blade.php tidak ditemukan.'
        );

        $dipakai = str_contains($this->isiView('menu/index'), '<x-alert')
            || str_contains($this->isiView('menu/create'), '<x-alert');

        $this->assertTrue(
            $dipakai,
            'Komponen <x-alert> belum dipakai di halaman daftar menu maupun halaman form.'
        );
    }

    /** Data menu tidak boleh ditampilkan mentah dengan {!! !!}. */
    public function test_data_menu_tidak_ditampilkan_mentah(): void
    {
        foreach (['menu/index', 'menu/show', 'menu/create', 'menu/partials/kartu'] as $view) {
            $this->assertStringNotContainsString(
                '{!!',
                $this->isiView($view),
                "Berkas {$view}.blade.php memakai {!! !!}. "
                . 'Data yang berasal dari pengguna wajib ditampilkan dengan {{ }}.'
            );
        }
    }

    /** Daftar menu harus menyiapkan tampilan untuk keadaan kosong. */
    public function test_daftar_kosong_menampilkan_pesan(): void
    {
        $isi = $this->isiView('menu/index');

        $this->assertStringContainsString(
            '@forelse',
            $isi,
            'Halaman daftar menu belum memakai @forelse.'
        );

        $this->assertStringContainsString(
            '@empty',
            $isi,
            'Halaman daftar menu belum memuat @empty sebagai pesan saat menu kosong.'
        );
    }

    /** @return string[] */
    private function semuaBerkasBlade(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'))
        );

        $berkas = [];

        foreach ($iterator as $item) {
            if ($item->isFile() && str_ends_with($item->getFilename(), '.blade.php')) {
                $berkas[] = $item->getPathname();
            }
        }

        return $berkas;
    }
}
