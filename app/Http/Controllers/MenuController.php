<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    private function api(): PendingRequest
    {
        return Http::baseUrl(config('services.menu_api.url'))
            ->withHeaders(['X-API-KEY' => config('services.menu_api.key')])
            ->timeout(5);
    }

    public function index(Request $request)
    {
        try {
            $menu = $this->api()->get('/menus', [
                'category' => $request->category,
            ]);

            $categories = $this->api()->get('/categories');

            if ($menu->failed() || $categories->failed()) {
            return $this->kosong();
            }

            return view('menu.index', [
                'menu' => $menu->object()->data,
                'categories' => $categories->object()->data,
                'dipilih' => $request->category,
                'gagal' => false,
            ]);
        } catch (ConnectionException $e) {
            return $this->kosong();
        }
    }
    
    public function show(int $menu)
    {
        $res = $this->api()->get('/menus/' . $menu);

        abort_if($res->status() === 404, 404);

        return view('menu.show', [
            'menu' => $res->object()->data,
        ]);
    }

    public function create()
    {
        Gate::authorize('manage-menu');

        $categories = $this->api()->get('/categories');

        return view('menu.create', [
            'categories' => $categories->object()->data,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-menu');

        $res = $this->api()->post('/menus', $request->only(
            'category_id',
            'name',
            'price',
            'stock',
            'note',
        ));

        if ($res->status() === 422) {
            return back()->withInput()->withErrors($res->json('errors'));
        }

        return to_route('menu.index')->with(
            'status',
            'Menu "' . $res->object()->data->name . '" berhasil ditambahkan.',
        );
    }

    public function edit(int $menu)
    {
        Gate::authorize('manage-menu');

        $res = $this->api()->get('/menus/' . $menu);

        abort_if($res->status() === 404, 404);

        $categories = $this->api()->get('/categories');

        return view('menu.edit', [
            'menu' => $res->object()->data,
            'categories' => $categories->object()->data,
        ]);
    }

    public function update(Request $request, int $menu)
    {
        Gate::authorize('manage-menu');

        $res = $this->api()->put('/menus/' . $menu, $request->only(
            'category_id',
            'name',
            'price',
            'stock',
            'note',
        ));

        if ($res->status() === 422) {
            return back()->withInput()->withErrors($res->json('errors'));
        }

        return to_route('menu.index')->with(
            'status',
            'Menu diperbarui.',
        );
    }

    public function destroy(int $menu)
    {
        Gate::authorize('delete-menu');

        $res = $this->api()->delete('/menus/' . $menu);

        abort_if($res->status() === 404, 404);

        return to_route('menu.index')->with(
            'status',
            'Menu dihapus.',
        );
    }

    private function kosong()
    {
        return view('menu.index', [
            'menu' => [],
            'categories' => [],
            'dipilih' => null,
            'gagal' => true,
        ]);
    }
}