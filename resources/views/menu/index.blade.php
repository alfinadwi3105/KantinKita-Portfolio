@extends('layouts.app')

@section('judul', 'Daftar Menu')

@section('isi')

<h2>Daftar Menu Kantin FIT</h2>

@session('status')
    <x-alert tipe="sukses">{{ $value }}</x-alert>
@endsession

<p>
    <a class="tombol" href="{{ route('menu.create') }}">
        + Tambah Menu
    </a>
</p>

<form method="GET" action="{{ route('menu.index') }}">
    <label for="category">Filter kategori:</label>

    <select id="category" name="category">
        <option value="">Semua kategori</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected($dipilih == $category->id)
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="tombol">
        Filter
    </button>
</form>

<ul class="daftar-menu">
    @forelse ($menu as $item)
        @include('menu.partials.kartu', ['item' => $item])
    @empty
        <li class="kartu-menu">
            Belum ada menu yang terdaftar.
        </li>
    @endforelse
</ul>

@endsection