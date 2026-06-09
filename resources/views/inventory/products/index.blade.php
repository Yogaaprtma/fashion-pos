@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Daftar Produk')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('inventory.products.index') }}" method="GET" class="flex-between" style="width:100%">
            <div style="display:flex;gap:10px">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, SKU, brand..." value="{{ request('search') }}" style="width:250px">
                <select name="category_id" class="form-control" style="width:200px">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="stock_status" class="form-control" style="width:150px">
                    <option value="">Semua Stok</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Rendah</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis</option>
                </select>
                <button type="submit" class="btn btn-secondary">Cari</button>
                @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                    <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary" style="color:var(--color-danger)">Reset</a>
                @endif
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('inventory.barcode-generator') }}" class="btn btn-secondary">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Cetak Barcode
                </a>
                <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th width="60">Foto</th>
                    <th>Info Produk</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>Stok Total</th>
                    <th>Status</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td>{{ $products->firstItem() + $index }}</td>
                    <td>
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary)">{{ $product->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">SKU: {{ $product->sku }} {{ $product->brand ? '· '.$product->brand : '' }}</div>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $product->category?->name ?? '-' }}</span>
                    </td>
                    <td class="currency">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $product->isLowStock() ? 'badge-danger' : 'badge-success' }}">
                            {{ $product->total_stock }}
                        </span>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $product->variants->count() }} varian</div>
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-sm btn-secondary btn-icon" title="Detail">👁️</a>
                            <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-sm btn-secondary btn-icon" title="Edit">✏️</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <div style="font-size:32px;margin-bottom:8px">📦</div>
                        Tidak ada produk ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection
