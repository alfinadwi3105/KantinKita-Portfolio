<?php

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tp051LayananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.key' => 'rahasia-kantin-fit']);
    }

    public function test_api_categories_menampilkan_data_kategori(): void
    {
        Category::create(['name' => 'Minuman']);
        Category::create(['name' => 'Makanan']);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Makanan')
            ->assertJsonPath('data.1.name', 'Minuman');
    }

    public function test_api_menus_menampilkan_data_menu_dan_kategori(): void
    {
        $category = Category::create(['name' => 'Makanan']);

        Menu::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
            'note' => 'Pakai telur',
        ]);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->getJson('/api/menus');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Nasi Goreng')
            ->assertJsonPath('data.0.price', 15000)
            ->assertJsonPath('data.0.stock', 10)
            ->assertJsonPath('data.0.category.name', 'Makanan');
    }

    public function test_api_menus_dapat_difilter_berdasarkan_kategori(): void
    {
        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);

        Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        Menu::create([
            'category_id' => $minuman->id,
            'name' => 'Es Teh',
            'price' => 5000,
            'stock' => 20,
        ]);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->getJson('/api/menus?category=' . $makanan->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Nasi Goreng');
    }

    public function test_api_menus_dapat_menampilkan_detail_menu(): void
    {
        $category = Category::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Ayam Geprek',
            'price' => 16000,
            'stock' => 8,
            'note' => 'Pedas',
        ]);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->getJson('/api/menus/' . $menu->id);

        $response->assertOk()
            ->assertJsonPath('data.id', $menu->id)
            ->assertJsonPath('data.name', 'Ayam Geprek')
            ->assertJsonPath('data.category.name', 'Makanan');
    }
}
