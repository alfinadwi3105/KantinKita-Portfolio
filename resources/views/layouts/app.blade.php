
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <header style="padding: 20px 25px 10px;">
        <h1 style="margin: 0;">KantinKita</h1>
    </header>

    <nav style="display: flex; align-items: center; gap: 25px; padding: 15px 25px; border-bottom: 1px solid #ddd; background: #fff;">

        <a href="{{ url('/') }}"
           style="text-decoration: none; color: #333;">
            Beranda
        </a>

        <a href="{{ route('menu.index') }}"
           style="text-decoration: none; color: #333;">
            Menu
        </a>

        <a href="{{ route('antrian') }}"
            style="text-decoration: none; color: #333;">
            Antrian
        </a>
        <a href="{{ url('/tentang') }}"
           style="text-decoration: none; color: #333;">
            Tentang
        </a>

        <div style="margin-left: auto; display: flex; align-items: center; gap: 12px;">

            @auth
                <span>{{ Auth::user()->name }}</span>

                <span style="font-size: 12px; color: #666;">
                    {{ Auth::user()->role }}
                </span>

                <form method="POST"
                      action="{{ route('logout') }}"
                      style="margin: 0;">
                    @csrf

                    <button type="submit"
                            style="background: none; border: none; padding: 0; color: #333; cursor: pointer; font: inherit;">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   style="text-decoration: none; color: #333;">
                    Masuk
                </a>
            @endauth

        </div>
    </nav>

    <main>
        @yield('isi')
    </main>

    <footer style="margin-top: 30px; padding: 15px 25px; border-top: 1px solid #ddd; color: #666;">
        <p>Praktikum PBW2 — D3 RPLA Telkom University</p>
    </footer>

</body>
</html>
