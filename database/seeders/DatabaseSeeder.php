<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Data menu sekarang milik backend. Frontend cuma punya akun petugas.
        $this->call([
            UserSeeder::class,
        ]);
    }
}