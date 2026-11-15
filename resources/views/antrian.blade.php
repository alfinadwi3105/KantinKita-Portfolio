@extends('layouts.app')
@section('judul', 'Antrian Pesanan')

@section('isi')
    <h2>Antrian Pesanan</h2>

    @if ($mati)
        <x-alert jenis="gagal">
            Layanan pesanan sedang tidak tersedia.
        </x-alert>
    @endif

    <p id="jam" class="kosong"></p>

    <table class="tabel">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="isi"></tbody>
    </table>

    <script>
        async function muat() {
            const res = await fetch('{{ route('antrian.data') }}');
            const daftar = await res.json();

            document.getElementById('isi').innerHTML = daftar.length
                ? daftar.map(a => `<tr><td>${a.kode}</td>
                    <td>${a.menu}</td><td>${a.qty}</td>
                    <td>${a.status}</td></tr>`).join('')
                : '<tr><td colspan="4">Belum ada pesanan.</td></tr>';

            document.getElementById('jam').textContent =
                'Diperbarui ' + new Date().toLocaleTimeString('id-ID');
        }

        muat();
        setInterval(muat, 3000);
    </script>
@endsection
