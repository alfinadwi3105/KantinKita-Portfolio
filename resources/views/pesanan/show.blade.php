@extends('layouts.app')

@section('judul', 'Status Pesanan')

@section('isi')

<h2>Status Pesanan</h2>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
</script>

<div class="kartu-menu">

    <p>
        Nomor pesanan: {{ $pesanan->kode }}
    </p>

    <p>
        Nama menu: {{ $pesanan->menu }}
    </p>

    <p>
        Jumlah: {{ $pesanan->qty }}
    </p>

    <p>
        Total pembayaran:
        Rp{{ number_format($pesanan->total, 0, ',', '.') }}
    </p>

    <p>
        Status: {{ $pesanan->status }}
    </p>

    <button type="button" id="bayar" class="tombol">
        Bayar
    </button>

    <a href="{{ route('pesanan.show', $pesanan->kode) }}">
        Perbarui Status
    </a>

</div>

<script>
    document.getElementById('bayar').onclick = function () {
        snap.pay('{{ $pesanan->snap_token }}');
    };
</script>

@endsection