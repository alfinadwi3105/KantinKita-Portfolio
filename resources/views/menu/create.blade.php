@extends('layouts.app')

@section('judul', 'Tambah Menu')

@section('isi')

<h2>Tambah Menu Baru</h2>

@if ($errors->any())
    <x-alert tipe="gagal">
        Ada {{ $errors->count() }} isian yang perlu diperbaiki.
    </x-alert>
@endif

<form method="POST" action="{{ route('menu.store') }}">

    @csrf

    <div class="isian">
        <label for="nama">Nama menu</label>
        <input
            type="text"
            id="nama"
            name="nama"
            value="{{ old('nama') }}"
            class="@error('nama') salah @enderror"
        >

        @error('nama')
            <span class="pesan-salah">
                {{ $message }}
            </span>
        @enderror
    </div>

    <div class="isian">
        <label for="kategori">Kategori</label>

        <select
            id="kategori"
            name="kategori"
            class="@error('kategori') salah @enderror"
        >
            <option value="">-- pilih kategori --</option>

            <option
                value="makanan"
                @selected(old('kategori') === 'makanan')
            >
                Makanan
            </option>

            <option
                value="minuman"
                @selected(old('kategori') === 'minuman')
            >
                Minuman
            </option>
        </select>

        @error('kategori')
            <span class="pesan-salah">
                {{ $message }}
            </span>
        @enderror
    </div>

    <div class="isian">
        <label for="harga">Harga (rupiah)</label>

        <input
            type="text"
            id="harga"
            name="harga"
            value="{{ old('harga') }}"
            class="@error('harga') salah @enderror"
        >

        @error('harga')
            <span class="pesan-salah">
                {{ $message }}
            </span>
        @enderror
    </div>

    <div class="isian">
    <label for="stok">Stok</label>

    <input
        type="text"
        id="stok"
        name="stok"
        value="{{ old('stok') }}"
        class="@error('stok') salah @enderror"
    >

    @error('stok')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
    </div>

    <div class="isian">
        <label for="catatan">Catatan (boleh dikosongkan)</label>

        <textarea
            id="catatan"
            name="catatan"
            rows="3"
            class="@error('catatan') salah @enderror"
        >{{ old('catatan') }}</textarea>

        @error('catatan')
            <span class="pesan-salah">
                {{ $message }}
            </span>
        @enderror
    </div>

    <button type="submit" class="tombol">
        Simpan Menu
    </button>

    <a href="{{ route('menu.index') }}">
        Batal
    </a>

</form>

@endsection