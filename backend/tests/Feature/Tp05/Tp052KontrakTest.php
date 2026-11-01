<?php

namespace Tests\Feature\Tp05;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tp052KontrakTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.key' => 'rahasia-kantin-fit']);
    }

    public function test_api_menolak_permintaan_tanpa_api_key(): void
    {
        $response = $this->getJson('/api/menus');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Kunci API tidak sah.',
            ]);
    }

    public function test_api_menolak_api_key_yang_salah(): void
    {
        $response = $this->withHeader('X-API-KEY', 'kunci-salah')
            ->getJson('/api/menus');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Kunci API tidak sah.',
            ]);
    }

    public function test_api_dapat_menambahkan_menu(): void
    {
        $category = Category::create(['name' => 'Makanan']);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->postJson('/api/menus', [
                'category_id' => $category->id,
                'name' => 'Nasi Goreng',
                'price' => 15000,
                'stock' => 10,
                'note' => 'Pakai telur',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Nasi Goreng')
            ->assertJsonPath('data.price', 15000)
            ->assertJsonPath('data.category.name', 'Makanan');

        $this->assertDatabaseHas('menus', [
            'name' => 'Nasi Goreng',
            'price' => 15000,
        ]);
    }

    public function test_api_menolak_menu_dengan_data_tidak_valid(): void
    {
        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->postJson('/api/menus', [
                'category_id' => 999,
                'name' => '',
                'price' => 500,
                'stock' => -1,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'category_id',
                'name',
                'price',
                'stock',
            ]);
    }

    public function test_api_dapat_memperbarui_menu(): void
    {
        $category = Category::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->putJson('/api/menus/' . $menu->id, [
                'category_id' => $category->id,
                'name' => 'Nasi Goreng Spesial',
                'price' => 18000,
                'stock' => 15,
                'note' => 'Menu baru',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Nasi Goreng Spesial')
            ->assertJsonPath('data.price', 18000);

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Nasi Goreng Spesial',
            'price' => 18000,
        ]);
    }

    public function test_api_dapat_menghapus_menu(): void
    {
        $category = Category::create(['name' => 'Makanan']);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        $response = $this->withHeader('X-API-KEY', 'rahasia-kantin-fit')
            ->deleteJson('/api/menus/' . $menu->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }
}
