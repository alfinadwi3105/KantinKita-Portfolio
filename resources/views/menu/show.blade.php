@extends('layouts.app')

@section('judul', $menu['nama'])

@section('isi')

<h2>{{ $menu['nama'] }}</h2>

<div class="kartu-menu">

    <p>
        <span class="label">
            {{ $menu['kategori'] }}
        </span>
    </p>

    <p class="harga">
        Rp{{ number_format($menu['harga'], 0, ',', '.') }}
    </p>

    @if ($menu['stok'] === 0)
    <span class="label-habis">Habis</span>
    @else
    <span class="keterangan">
        Stok: {{ $menu['stok'] }}
    </span>
    @endif

    <p class="keterangan">
        Catatan: {{ $menu['catatan'] ?? '-' }}
    </p>

</div>

<p>
    <a href="{{ route('menu.index') }}">
        &laquo; Kembali ke daftar menu
    </a>
</p>

@endsectionudh