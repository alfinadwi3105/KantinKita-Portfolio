<div class="kartu-menu">

    <h3>{{ $item->name }}</h3>

    <p>
        <span class="label">
            {{ $item->category->name }}
        </span>
    </p>

    <p class="harga">
        Rp{{ number_format($item->price, 0, ',', '.') }}
    </p>

    @if ($item->stock === 0)
        <span class="label-habis">
            Habis
        </span>
    @else
        <span class="keterangan">
            Stok: {{ $item->stock }}
        </span>
    @endif

    <p class="keterangan">
        Catatan: {{ $item->note ?? '-' }}
    </p>

    <div class="keterangan">
        <a href="{{ route('menu.show', $item) }}">Lihat detail</a>

        @can('manage-menu')
            <a href="{{ route('menu.edit', $item) }}">Ubah</a>
        @endcan

        @can('delete-menu')
            <form method="POST" class="aksi-hapus"
                  action="{{ route('menu.destroy', $item) }}"
                  onsubmit="return confirm('Hapus menu?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="tombol-hapus">Hapus</button>
            </form>
        @endcan
    </div>

</div>