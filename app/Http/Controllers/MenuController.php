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
            ->withHeaders([
                'X-API-KEY' => config('services.menu_api.key')
            ])
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

            // Mengambil data cuaca
            $cuaca = null;

            try {
                $c = $this->api()->get('/cuaca');

                if ($c->successful()) {
                    $cuaca = $c->object()->data;
                }
            } catch (ConnectionException $e) {
                $cuaca = null;
            }

            return view('menu.index', [
                'menu' => $menu->object()->data,
                'categories' => $categories->object()->data,
                'dipilih' => $request->category,
                'gagal' => false,
                'cuaca' => $cuaca,
            ]);

        } catch (ConnectionException $e) {
            return $this->kosong();
        }
    }


    public function show(int $menu)
    {
        $res = $this->api()->get('/menus/' . $menu);

        abort_if($res->status() === 404, 404);

        abort_if($res->failed(), 503);

        return view('menu.show', [
            'menu' => $res->object()->data,
        ]);
    }

    public function create()
{
    Gate::authorize('manage-menu');

    try {
        $categories = $this->api()->get('/categories');

        if ($categories->failed()) {
            abort(503, 'Gagal mengambil data kategori dari backend.');
        }

        $data = $categories->object();

        if (!isset($data->data)) {
            abort(503, 'Format respons kategori dari backend tidak sesuai.');
        }

        return view('menu.create', [
            'categories' => $data->data,
        ]);

    } catch (ConnectionException $e) {
        abort(503, 'Backend API tidak dapat dihubungi.');
    }
}

    public function store(Request $request)
    {
        Gate::authorize('manage-menu');

        try {
            $res = $this->api()->post('/menus', $request->only(
                'category_id',
                'name',
                'price',
                'stock',
                'note',
            ));

            if ($res->status() === 422) {
                return back()
                    ->withInput()
                    ->withErrors($res->json('errors'));
            }

            if ($res->failed()) {
                abort(503, 'Gagal menyimpan menu ke backend.');
            }

            return to_route('menu.index')->with(
                'status',
                'Menu "' . $res->object()->data->name . '" berhasil ditambahkan.',
            );

        } catch (ConnectionException $e) {
            abort(503, 'Backend API tidak dapat dihubungi.');
        }
    }

    public function edit(int $menu)
    {
        Gate::authorize('manage-menu');

        try {
            $res = $this->api()->get('/menus/' . $menu);

            abort_if($res->status() === 404, 404);

            if ($res->failed()) {
                abort(503, 'Gagal mengambil data menu.');
            }

            $categories = $this->api()->get('/categories');

            if ($categories->failed()) {
                abort(503, 'Gagal mengambil data kategori.');
            }

            return view('menu.edit', [
                'menu' => $res->object()->data,
                'categories' => $categories->object()->data,
            ]);

        } catch (ConnectionException $e) {
            abort(503, 'Backend API tidak dapat dihubungi.');
        }
    }

    public function update(Request $request, int $menu)
    {
        Gate::authorize('manage-menu');

        try {
            $res = $this->api()->put('/menus/' . $menu, $request->only(
                'category_id',
                'name',
                'price',
                'stock',
                'note',
            ));

            if ($res->status() === 422) {
                return back()
                    ->withInput()
                    ->withErrors($res->json('errors'));
            }

            if ($res->failed()) {
                abort(503, 'Gagal memperbarui menu.');
            }

            return to_route('menu.index')->with(
                'status',
                'Menu diperbarui.',
            );

        } catch (ConnectionException $e) {
            abort(503, 'Backend API tidak dapat dihubungi.');
        }
    }

    public function destroy(int $menu)
    {
        Gate::authorize('delete-menu');

        try {
            $res = $this->api()->delete('/menus/' . $menu);

            abort_if($res->status() === 404, 404);

            if ($res->failed()) {
                abort(503, 'Gagal menghapus menu.');
            }

            return to_route('menu.index')->with(
                'status',
                'Menu dihapus.',
            );

        } catch (ConnectionException $e) {
            abort(503, 'Backend API tidak dapat dihubungi.');
        }
    }

    private function kosong()
    {
        return view('menu.index', [
            'menu' => [],
            'categories' => [],
            'dipilih' => null,
            'gagal' => true,
            'cuaca' => null,
        ]);
    }
}