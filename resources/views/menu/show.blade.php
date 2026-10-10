
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
    @can('manage-menu')
        <a class="tombol"
           href="{{ route('menu.edit', $menu) }}">Ubah menu ini</a>
    @endcan
    <a href="{{ route('menu.index') }}">&laquo; Kembali ke daftar menu</a>
</p>

@endsection