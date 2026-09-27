<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    private function menuAwal(): array
    {
        return [
            [
                'nama' => 'Nasi Goreng Spesial',
                'kategori' => 'makanan',
                'harga' => 18000,
                'stok' => 12,
                'catatan' => 'Pakai telur mata sapi'
            ],
            [
                'nama' => 'Ayam Geprek',
                'kategori' => 'makanan',
                'harga' => 16000,
                'stok' => 8,
                'catatan' => 'Level pedas bisa dipilih'
            ],
            [
                'nama' => 'Mie Ayam Bakso',
                'kategori' => 'makanan',
                'harga' => 15000,
                'stok' => 0,
                'catatan' => 'Tersedia mulai pukul 10.00'
            ],
            [
                'nama' => 'Es Teh Manis',
                'kategori' => 'minuman',
                'harga' => 5000,
                'stok' => 30,
                'catatan' => null
            ],
            [
                'nama' => 'Kopi Susu Gula Aren',
                'kategori' => 'minuman',
                'harga' => 12000,
                'stok' => 15,
                'catatan' => 'Favorit mahasiswa tingkat akhir'
            ],
        ];
    }

    private function semuaMenu(): array
    {
        $gabungan = array_merge(
            $this->menuAwal(),
            session('menu_tambahan', [])
        );

        foreach ($gabungan as $urutan => $item) {
            $gabungan[$urutan]['id'] = $urutan + 1;
            $gabungan[$urutan]['catatan'] = $item['catatan'] ?? null;
        }

        return $gabungan;
    }

    public function index()
    {
        return view('menu.index', [
            'menu' => $this->semuaMenu()
        ]);
    }

    public function create()
    {
        return view('menu.create');
    }

    public function store(Request $request)
{
    $tervalidasi = $request->validate(['stok' => ['required', 'integer', 'min:0'],
        'nama' => ['required', 'string', 'max:60'],
        'kategori' => ['required', 'in:makanan,minuman'],
        'harga' => ['required', 'numeric', 'min:1000'],
        'catatan' => ['nullable', 'string', 'max:255'],
    ], [
        'required' => 'Isian ini wajib diisi.',
        'nama.max' => 'Nama menu paling panjang 60 karakter.',
        'kategori.in' => 'Kategori hanya boleh makanan atau minuman.',
        'harga.numeric' => 'Harga harus berupa angka.',
        'harga.min' => 'Harga paling murah Rp1.000.',
        'catatan.max' => 'Catatan paling panjang 255 karakter.',
    ]);

    $tervalidasi['catatan'] = $tervalidasi['catatan'] ?? null;

    session()->push('menu_tambahan', $tervalidasi);

    return to_route('menu.index')
        ->with(
            'status',
            'Menu "' . $tervalidasi['nama'] . '" berhasil ditambahkan.'
        );
}

    public function show(string $menu)
    {
        $menu = collect($this->semuaMenu())
            ->firstWhere('id', (int) $menu);

        abort_if($menu === null, 404);

        return view('menu.show', [
            'menu' => $menu
        ]);
    }
}