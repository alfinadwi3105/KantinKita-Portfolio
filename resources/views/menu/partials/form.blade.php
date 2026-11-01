<div class="isian">
    <label for="category_id">Kategori</label>

    <select
        id="category_id"
        name="category_id"
        class="@error('category_id') salah @enderror"
    >
        <option value="">-- pilih kategori --</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(old('category_id', $menu->category->id ?? null) == $category->id)
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    @error('category_id')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="isian">
    <label for="name">Nama menu</label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $menu->name ?? '') }}"
        class="@error('name') salah @enderror"
    >

    @error('name')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="isian">
    <label for="price">Harga (rupiah)</label>

    <input
        type="text"
        id="price"
        name="price"
        value="{{ old('price', $menu->price ?? '') }}"
        class="@error('price') salah @enderror"
    >

    @error('price')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="isian">
    <label for="stock">Stok</label>

    <input
        type="text"
        id="stock"
        name="stock"
        value="{{ old('stock', $menu->stock ?? '') }}"
        class="@error('stock') salah @enderror"
    >

    @error('stock')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
</div>

<div class="isian">
    <label for="note">Catatan (boleh dikosongkan)</label>

    <textarea
        id="note"
        name="note"
        rows="3"
        class="@error('note') salah @enderror"
    >{{ old('note', $menu->note ?? '') }}</textarea>

    @error('note')
        <span class="pesan-salah">
            {{ $message }}
        </span>
    @enderror
</div>