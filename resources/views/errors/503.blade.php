@extends('layouts.app')

@section('judul', 'Layanan Tidak Tersedia')

@section('isi')
    <div class="text-center">
        <h1>503</h1>
        <h2>Layanan Tidak Tersedia</h2>
        <p>Maaf, layanan sedang mengalami gangguan. Silakan coba lagi nanti.</p>
        <a href="{{ route('menu.index') }}">Kembali ke Menu</a>
    </div>
@endsection