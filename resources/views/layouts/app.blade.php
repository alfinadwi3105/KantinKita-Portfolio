
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <header>
            <h1>KantinKita</h1>

            <div class="aksi-login">
        @auth
            <span class="nama-pengguna">{{ Auth::user()->name }}</span>
            <span class="badge-peran">{{ Auth::user()->role }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="tombol">Keluar</button>
            </form>
        @else
             <a href="{{ route('login') }}" class="tombol">Masuk</a>
        @endauth
    </div>
        </div>
    </header>

    <main>
        @yield('isi')
    </main>

    <footer>
        <p>Praktikum PBW2 — D3 RPLA Telkom University</p>
    </footer>

</body>
</html>