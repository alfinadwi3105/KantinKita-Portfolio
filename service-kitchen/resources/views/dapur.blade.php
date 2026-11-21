<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Layar Dapur · Kantin FIT</title>
    <style>
        body   { font-family: system-ui, sans-serif; margin: 2rem; }
        .kartu { border: 2px solid #1F8A4C; border-radius: .6rem;
                 padding: .9rem 1.1rem; margin-bottom: .7rem;
                 display: flex; justify-content: space-between;
                 align-items: center; }
        .kode { font-weight: 700; font-size: 1.05rem; }
        .rinci { color: #666; font-size: .9rem; }
        .status { font-weight: 700; color: #1F8A4C; }
        .kosong { color: #888; }
        .kartu.dimasak { border-color: #B46A00; background: #FFF6E9;}
        .kartu.dimasak .status { color: #B46A00;}
    </style>
</head>
<body>
    <h1>Pesanan Masuk</h1>
    <p id="jam" class="kosong"></p>

    <div id="isi"></div>

    <script>
        const token =
            document.querySelector('meta[name=csrf-token]').content;

        async function mulai(kode) {
            await fetch(`/dapur/${kode}/mulai`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
            });
            muat();
        }

        async function muat() {
            const res = await fetch('/dapur/data');
            const daftar = await res.json();

            document.getElementById('isi').innerHTML = daftar.length
                ? daftar.map(p => `
                    <div class="kartu ${p.status}">
                      <div>
                        <div class="kode">${p.kode}</div>
                        <div class="rinci">${p.menu} &times; ${p.qty}</div>
                      </div>
                      <div class="status">
                        ${p.status}
                        ${p.status === 'masuk'
                            ? `<button onclick="mulai('${p.kode}')">
                                Mulai Masak</button>`
                            : ''}
                      </div>
                    </div>`).join('')
                : '<p class="kosong">Belum ada pesanan.</p>';

            document.getElementById('jam').textContent =
                'Diperbarui ' + new Date().toLocaleTimeString('id-ID');
        }

        muat();
        setInterval(muat, 3000);
    </script>
</body>
</html>
