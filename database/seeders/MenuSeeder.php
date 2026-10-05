<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::where('name', 'Makanan')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Nasi Goreng Spesial',
            'price' => 18000,
            'stock' => 12,
            'note' => 'Pakai telur mata sapi',
        ]);

        Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Ayam Geprek',
            'price' => 16000,
            'stock' => 8,
            'note' => 'Level pedas bisa dipilih',
        ]);

        Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Mie Ayam Bakso',
            'price' => 15000,
            'stock' => 0,
            'note' => 'Tersedia mulai pukul 10.00',
        ]);

        Menu::create([
            'category_id' => $minuman->id,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'stock' => 30,
            'note' => null,
        ]);

        Menu::create([
            'category_id' => $minuman->id,
            'name' => 'Kopi Susu Gula Aren',
            'price' => 12000,
            'stock' => 15,
            'note' => 'Favorit mahasiswa tingkat akhir',
        ]);
    }
}