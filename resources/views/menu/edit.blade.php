@extends('layouts.app')

@section('judul', 'Ubah Menu')

@section('isi')

<h2>Ubah Menu</h2>

@if ($errors->any())
    <x-alert tipe="gagal">
        Ada {{ $errors->count() }} isian yang perlu diperbaiki.
    </x-alert>
@endif

<form method="POST" action="{{ route('menu.update', $menu->id) }}">
    @csrf
    @method('PUT')

    @include('menu.partials.form')

    <button type="submit" class="tombol">
        Simpan Perubahan
    </button>

    <a href="{{ route('menu.show', $menu->id) }}">
        Batal
    </a>
</form>

@endsection