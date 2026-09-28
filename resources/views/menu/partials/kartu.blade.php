<li class="kartu-menu">
    <span class="nama">
        {{ $item->name }}
    </span>

    <span class="label">
        {{ $item->category->name }}
    </span>

    <span class="harga">
        Rp{{ number_format($item->price, 0, ',', '.') }}
    </span>

    @if ($item->stock === 0)
        <span class="label-habis">
            Habis
        </span>
    @else
        <span class="keterangan">
            Stok: {{ $item->stock }}
        </span>
    @endif

    <div class="keterangan">
        <a href="{{ route('menu.show', $item) }}">
            Lihat detail
        </a>
    </div>
</li>