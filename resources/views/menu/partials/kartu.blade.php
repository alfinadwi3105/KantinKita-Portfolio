<li class="kartu-menu">
    <span class="nama">
        {{ $item['nama'] }}
    </span>

    <span class="label">
        {{ $item['kategori'] }}
    </span>

    <span class="harga">
        Rp{{ number_format($item['harga'], 0, ',', '.') }}
    </span>

    @if ($item['stok'] === 0)
        <span class="label-habis">Habis</span>
    @else
        <span class="keterangan">
            Stok: {{ $item['stok'] }}
        </span>
    @endif

    <div class="keterangan">
        <a href="{{ route('menu.show', $item['id']) }}">
            Lihat detail
        </a>
    </div>
</li>