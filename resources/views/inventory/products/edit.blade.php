@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk: ' . $product->name)

@section('content')
<div class="card" style="max-width:800px;margin:0 auto">
    <div class="card-body">
        <form action="{{ route('inventory.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-2" style="gap:16px;margin-bottom:16px">
                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @foreach($cat->children ?? [] as $child)
                                <option value="{{ $child->id }}" {{ $product->category_id == $child->id ? 'selected' : '' }}>-- {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli Dasar (Rp) *</label>
                    <input type="number" name="buy_price" class="form-control" value="{{ old('buy_price', (int)$product->buy_price) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual Dasar (Rp) *</label>
                    <input type="number" name="sell_price" class="form-control" value="{{ old('sell_price', (int)$product->sell_price) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Merek / Brand</label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Stok Rendah</label>
                    <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" min="0" required>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Status Produk</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $product->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Deskripsi Produk</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
