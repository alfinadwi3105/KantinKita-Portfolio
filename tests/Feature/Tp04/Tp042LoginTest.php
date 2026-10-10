<?php

namespace Tests\Feature\Tp04;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Bagian B — Login dan Logout (30 poin).
 *
 * Yang diperiksa: halaman login tampil, kredensial benar membuat session
 * login, kredensial salah ditolak, logout mengakhiri session, dan halaman
 * menu tertutup untuk tamu.
 *
 *   php artisan test --filter=Tp042Login
 */
class Tp042LoginTest extends TestCase
{
    use RefreshDatabase;

    private function pengguna(array $ubah = []): User
    {
        return User::create(array_merge([
            'name'     => 'Kasir Uji',
            'email'    => 'uji@kantinfit.id',
            'password' => 'rahasia123',
            'role'     => 'kasir',
        ], $ubah));
    }

    /** Route bernama login harus ada — middleware auth memerlukannya. */
    public function test_route_login_terdaftar(): void
    {
        $this->assertNotNull(
            Route::getRoutes()->getByName('login'),
            'Beri nama "login" pada route GET /login, karena middleware auth mengalihkan ke sana.'
        );
    }

    /** Halaman login harus bisa dibuka tamu dan memuat form. */
    public function test_halaman_login_tampil(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('_token', false);
    }

    /** Kredensial yang benar membuat pengguna masuk. */
    public function test_login_dengan_kredensial_benar(): void
    {
        $user = $this->pengguna();

        $this->post('/login', [
            'email'    => 'uji@kantinfit.id',
            'password' => 'rahasia123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    /** Password salah harus ditolak dan menampilkan pesan. */
    public function test_login_dengan_password_salah_ditolak(): void
    {
        $this->pengguna();

        $this->from('/login')->post('/login', [
            'email'    => 'uji@kantinfit.id',
            'password' => 'salah-sekali',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** Isian kosong harus ditolak validasi. */
    public function test_login_tanpa_isian_ditolak(): void
    {
        $this->from('/login')->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    /** Session harus diganti setelah login berhasil. */
    public function test_session_diganti_setelah_login(): void
    {
        $this->pengguna();

        $this->get('/login');
        $sebelum = session()->getId();

        $this->post('/login', [
            'email'    => 'uji@kantinfit.id',
            'password' => 'rahasia123',
        ]);

        $this->assertNotSame(
            $sebelum,
            session()->getId(),
            'Panggil $request->session()->regenerate() tepat setelah Auth::attempt() berhasil.'
        );
    }

    /** Logout mengakhiri session login. */
    public function test_logout_mengakhiri_session(): void
    {
        $user = $this->pengguna();

        $this->actingAs($user)->post('/logout')->assertRedirect();

        $this->assertGuest();
    }

    /** Tamu tidak boleh membuka daftar menu. */
    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/menu')->assertRedirect('/login');
    }

    /** Pengguna yang sudah login tidak perlu melihat halaman login lagi. */
    public function test_pengguna_login_tidak_melihat_halaman_login(): void
    {
        $this->actingAs($this->pengguna())
            ->get('/login')
            ->assertRedirect();
    }

    /** Nama dan peran pengguna ditampilkan di layout. */
    public function test_nama_dan_peran_tampil_setelah_login(): void
    {
        $user = $this->pengguna(['name' => 'Kasir Kantin']);

        $this->actingAs($user)
            ->get('/menu')
            ->assertOk()
            ->assertSee('Kasir Kantin')
            ->assertSee('kasir');
    }
}