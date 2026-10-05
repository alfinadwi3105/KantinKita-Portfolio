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

    @include('menu.partials.form')

    <button type="submit" class="tombol">
        Simpan Menu
    </button>

    <a href="{{ route('menu.index') }}">
        Batal
    </a>
</form>

@endsection