<?php

namespace Tests\Feature\Tp05;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Tp054ChallengeTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_menu_tetap_tampil_jika_api_gagal(): void
    {
        Http::fake([
            '*/menus*' => Http::response([], 503),
            '*/categories*' => Http::response([], 503),
        ]);

        $this->actingAs(User::factory()->create());

        $response = $this->get(route('menu.index'));

        $response->assertOk();
        $response->assertViewIs('menu.index');
        $response->assertViewHas('gagal', true);
    }
}