<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Menampilkan semua menu
    public function index(Request $request)
    {
        $menus = Menu::with('category')
            ->when($request->category, function ($query, $id) {
                $query->where('category_id', $id);
            })
            ->orderBy('name')
            ->get();

        return MenuResource::collection($menus);
    }

    // Menampilkan satu menu berdasarkan ID
    public function show(Menu $menu)
    {
        return new MenuResource($menu->load('category'));
    }

    // Menambahkan menu baru
    public function store(Request $request)
    {
        $tervalidasi = $request->validate(
            $this->aturan(),
            $this->pesan()
        );

        $menu = Menu::create($tervalidasi);

        return (new MenuResource($menu->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    // Mengubah data menu
    public function update(Request $request, Menu $menu)
    {
        $tervalidasi = $request->validate(
            $this->aturan(),
            $this->pesan()
        );

        $menu->update($tervalidasi);

        return new MenuResource($menu->load('category'));
    }

    // Menghapus menu
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->noContent();
    }

    // Aturan validasi
    protected function aturan(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    // Pesan kesalahan validasi
    protected function pesan(): array
    {
        return [
            'required' => 'Isian ini wajib diisi.',
            'name.max' => 'Nama menu paling panjang 60 karakter.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga paling murah Rp1.000.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'note.max' => 'Catatan paling panjang 255 karakter.',
        ];
    }
}
