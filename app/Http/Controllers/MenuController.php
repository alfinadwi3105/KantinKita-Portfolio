<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
   public function index(Request $request)
{
    $menu = Menu::with('category')
        ->when($request->category, function ($query, $category) {
            $query->where('category_id', $category);
        })
        ->orderBy('name')
        ->get();

    $categories = Category::orderBy('name')->get();

    return view('menu.index', [
        'menu' => $menu,
        'categories' => $categories,
        'dipilih' => $request->category,
    ]);
}

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('menu.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $tervalidasi = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:60'],
            'price' => ['required', 'numeric', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        Menu::create($tervalidasi);

        return to_route('menu.index')
            ->with(
                'status',
                'Menu "' . $tervalidasi['name'] . '" berhasil ditambahkan.'
            );
    }

    public function show(Menu $menu)
    {
        $menu->load('category');

        return view('menu.show', [
            'menu' => $menu,
        ]);
    }

    public function edit(Menu $menu)
    {
        $categories = Category::orderBy('name')->get();

        return view('menu.edit', [
            'menu' => $menu,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $tervalidasi = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:60'],
            'price' => ['required', 'numeric', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $menu->update($tervalidasi);

        return to_route('menu.show', $menu)
            ->with(
                'status',
                'Menu "' . $menu->name . '" berhasil diperbarui.'
            );
    }

    public function destroy(Menu $menu)
    {
        $nama = $menu->name;

        $menu->delete();

        return to_route('menu.index')
            ->with(
                'status',
                'Menu "' . $nama . '" berhasil dihapus.'
            );
    }
}