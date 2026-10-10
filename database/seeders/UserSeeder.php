<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = [
            [
                'name' => 'Admin Kantin',
                'email' => 'admin@kantinfit.id',
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir Kantin',
                'email' => 'kasir@kantinfit.id',
                'role' => 'kasir',
            ],
        ];

        foreach ($pengguna as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => 'rahasia123',
                ]
            );
        }
    }
}