
@extends('layouts.app')

@section('judul', 'Masuk')

@section('isi')
    <div class="kartu-login">
        <h2>Masuk ke Kantin FIT</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="isian">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" autofocus
                       class="@error('email') salah @enderror">
                @error('email')
                    <span class="pesan-salah">{{ $message }}</span>
                @enderror
            </div>

            <div class="isian">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       class="@error('password') salah @enderror">
                @error('password')
                    <span class="pesan-salah">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="tombol">Masuk</button>
        </form>
    </div>
@endsection