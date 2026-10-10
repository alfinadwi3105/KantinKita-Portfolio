<?php

namespace Tests\Feature\Tp04;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Bagian A — Pengguna dan Peran (25 poin).
 *
 * Yang diperiksa: kolom role ada dan punya default, model User mengizinkan
 * role diisi, seeder membuat akun admin dan kasir, dan password benar-benar
 * tersimpan dalam bentuk hash.
 *
 *   php artisan test --filter=Tp041Pengguna
 */
class Tp041PenggunaTest extends TestCase
{
    use RefreshDatabase;

    /** Tabel users harus punya kolom role. */
    public function test_tabel_users_punya_kolom_role(): void
    {
        $this->assertTrue(
            Schema::hasColumn('users', 'role'),
            'Tabel users belum punya kolom role. Buat migration add_role_to_users_table.'
        );
    }

    /** Pengguna baru tanpa role harus otomatis menjadi kasir. */
    public function test_role_default_adalah_kasir(): void
    {
        $user = User::create([
            'name'     => 'Pengguna Baru',
            'email'    => 'baru@kantinfit.id',
            'password' => 'rahasia123',
        ]);

        $this->assertSame(
            'kasir',
            $user->fresh()->role,
            'Kolom role harus punya default "kasir" supaya pengguna baru mendapat hak paling kecil.'
        );
    }

    /** Kolom role harus boleh diisi lewat mass assignment. */
    public function test_role_boleh_diisi_lewat_create(): void
    {
        $user = User::create([
            'name'     => 'Admin Uji',
            'email'    => 'adminuji@kantinfit.id',
            'password' => 'rahasia123',
            'role'     => 'admin',
        ]);

        $this->assertSame(
            'admin',
            $user->fresh()->role,
            'Tambahkan "role" ke daftar #[Fillable] pada model User.'
        );
    }

    /** UserSeeder harus membuat akun admin dan kasir. */
    public function test_seeder_membuat_akun_admin_dan_kasir(): void
    {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@kantinfit.id',
            'role'  => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'kasir@kantinfit.id',
            'role'  => 'kasir',
        ]);
    }

    /** Seeder boleh dijalankan berkali-kali tanpa menggandakan baris. */
    public function test_seeder_boleh_dijalankan_dua_kali(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(
            1,
            User::where('email', 'admin@kantinfit.id')->count(),
            'Pakai firstOrCreate atau updateOrCreate supaya seeder aman dijalankan ulang.'
        );
    }

    /** Password tidak boleh tersimpan sebagai teks biasa. */
    public function test_password_tersimpan_dalam_bentuk_hash(): void
    {
        $this->seed(UserSeeder::class);

        $admin = User::where('email', 'admin@kantinfit.id')->first();

        $this->assertNotNull($admin, 'Akun admin@kantinfit.id tidak ditemukan.');

        $this->assertNotSame(
            'rahasia123',
            $admin->password,
            'Password tersimpan apa adanya. Pastikan casts() berisi "password" => "hashed".'
        );

        $this->assertTrue(
            Hash::check('rahasia123', $admin->password),
            'Password tersimpan, tetapi tidak cocok saat diperiksa dengan Hash::check().'
        );
    }
}
