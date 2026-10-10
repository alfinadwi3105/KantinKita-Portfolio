@extends('layouts.app')
@section('judul', 'Akses ditolak')
@section('isi')
    <div class="halaman-403">
        <p class="kode">403</p>

        <h2>Akses ditolak</h2>

        <p class="pesan-403">
            Kamu sudah masuk, tetapi peranmu tidak punya hak untuk
            melakukan aksi ini.
        </p>

        <p>
            <a class="tombol tombol-403" href="{{ route('menu.index') }}">
                Kembali ke daftar menu
            </a>
        </p>
    </div>
@endsection