@extends('layouts.app')

@section('judul', $menu->name)

@section('isi')

<h2>{{ $menu->name }}</h2>

<div class="kartu-menu">

    <p>
        <span class="label">
            {{ $menu->category->name }}
        </span>
    </p>

    <p class="harga">
        Rp{{ number_format($menu->price, 0, ',', '.') }}
    </p>

    @if ($menu->stock === 0)
        <span class="label-habis">
            Habis
        </span>
    @else
        <span class="keterangan">
            Stok: {{ $menu->stock }}
        </span>
    @endif

    <p class="keterangan">
        Catatan: {{ $menu->note ?? '-' }}
    </p>

</div>

<p>
    <a href="{{ route('menu.edit', $menu) }}">
        Ubah Menu
    </a>
</p>

<form
    method="POST"
    action="{{ route('menu.destroy', $menu) }}"
    onsubmit="return confirm('Yakin ingin menghapus menu ini?')"
>
    @csrf
    @method('DELETE')

    <button type="submit" class="tombol">
        Hapus Menu
    </button>
</form>

<p>
    <a href="{{ route('menu.index') }}">
        &laquo; Kembali ke daftar menu
    </a>
</p>

@endsection