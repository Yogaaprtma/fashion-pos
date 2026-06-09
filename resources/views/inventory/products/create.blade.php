@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="card" style="max-width:800px;margin:0 auto">
    <div class="card-header">
        <div class="card-title">Informasi Produk</div>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-2" style="gap:16px;margin-bottom:16px">
                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @foreach($cat->children ?? [] as $child)
                                <option value="{{ $child->id }}">-- {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli Dasar (Rp) *</label>
                    <input type="number" name="buy_price" class="form-control" value="{{ old('buy_price') }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual Dasar (Rp) *</label>
                    <input type="number" name="sell_price" class="form-control" value="{{ old('sell_price') }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Merek / Brand</label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Stok Rendah</label>
                    <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" min="0" required>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Deskripsi Produk</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Foto Produk</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                    <small style="color:var(--text-muted)">Bisa pilih lebih dari satu gambar. Gambar pertama akan menjadi foto utama.</small>
                </div>
            </div>

            <hr style="margin:24px 0;border-color:var(--border)">

            <div class="flex-between" style="margin-bottom:16px">
                <div class="card-title">Varian Produk</div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addVariant()">+ Tambah Varian</button>
            </div>

            <div id="variantsContainer">
                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 40px;gap:10px;margin-bottom:10px;align-items:end">
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Ukuran</label>
                        <input type="text" name="variants[0][size]" class="form-control" placeholder="M, L, XL, 32...">
                    </div>
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Warna</label>
                        <input type="text" name="variants[0][color]" class="form-control" placeholder="Hitam, Putih...">
                    </div>
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="variants[0][stock_qty]" class="form-control" value="0" min="0" required>
                    </div>
                    <button type="button" class="btn btn-secondary btn-icon" onclick="this.parentElement.remove()" disabled>✕</button>
                </div>
            </div>

            <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
    let variantIndex = 1;
    function addVariant() {
        const container = document.getElementById('variantsContainer');
        const row = document.createElement('div');
        row.className = 'variant-row';
        row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr 40px;gap:10px;margin-bottom:10px;align-items:end';
        row.innerHTML = `
            <div class="form-group" style="margin:0">
                <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="M, L, XL, 32...">
            </div>
            <div class="form-group" style="margin:0">
                <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Hitam, Putih...">
            </div>
            <div class="form-group" style="margin:0">
                <input type="number" name="variants[${variantIndex}][stock_qty]" class="form-control" value="0" min="0" required>
            </div>
            <button type="button" class="btn btn-secondary btn-icon" onclick="this.parentElement.remove()">✕</button>
        `;
        container.appendChild(row);
        variantIndex++;
        
        // Enable all delete buttons if > 1
        const rows = document.querySelectorAll('.variant-row');
        rows.forEach((r, idx) => {
            const btn = r.querySelector('button');
            if (btn) btn.disabled = rows.length === 1;
        });
    }
</script>
@endsection
