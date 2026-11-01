<?php

namespace Tests\Feature\Tp05;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Tp053KonsumsiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_menu_mengambil_data_dari_api(): void
    {
        Http::fake([
            '*/menus*' => Http::response([
                'data' => [],
            ], 200),

            '*/categories*' => Http::response([
                'data' => [],
            ], 200),
        ]);

        $this->actingAs(User::factory()->create());

        $response = $this->get(route('menu.index'));

        $response->assertStatus(200);
        $response->assertViewIs('menu.index');
        $response->assertViewHas('menu');
        $response->assertViewHas('categories');

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), '/menus')
                && $request->hasHeader(
                    'X-API-KEY',
                    config('services.menu_api.key')
                );
        });

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), '/categories')
                && $request->hasHeader(
                    'X-API-KEY',
                    config('services.menu_api.key')
                );
        });
    }
}
