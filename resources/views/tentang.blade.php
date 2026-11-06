@extends('layouts.app')

@section('judul', 'Tentang')

@section('isi')

    <h2>Tentang Aplikasi Ini</h2>

    <p>Aplikasi ini dibuat untuk praktikum Pemrograman Web 2.</p>

    <ul>
        <li>Nama : {{ $nama }}</li>
        <li>NIM : {{ $nim }}</li>
        <li>Kelas : {{ $kelas }}</li>
    </ul>

    <h2>Peta Kantin FIT</h2>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>

    <div id="peta" style="height: 400px;"></div>

    <script>
        const lat = {{ config('services.peta.lat') }};
        const lon = {{ config('services.peta.lon') }};

        const peta = L.map('peta').setView([lat, lon], 17);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(peta);

        L.marker([lat, lon])
            .addTo(peta)
            .bindPopup('Kantin FIT');
    </script>

@endsection