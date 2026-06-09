@extends('layouts.app')

@section('title', 'Detail Produk')
@section('page-title')
    <a href="{{ route('inventory.products.index') }}" style="color:var(--text-muted);text-decoration:none;font-weight:400;margin-right:8px;">&larr; Kembali</a>
    Detail: {{ $product->name }}
@endsection

@section('content')

<div class="grid" style="grid-template-columns: 300px 1fr; gap: 24px; align-items: start;">
    
    <!-- Info Produk -->
    <div class="card">
        <div class="card-body">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:100%; height:auto; aspect-ratio:1/1; object-fit:cover; border-radius:8px; border:1px solid var(--border); margin-bottom:16px;">
            <div style="font-size:20px; font-weight:700; margin-bottom:4px;">{{ $product->name }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:16px;">SKU: {{ $product->sku }}</div>
            
            <table class="table" style="font-size:13px; background:transparent;">
                <tbody>
                    <tr>
                        <td style="color:var(--text-muted); border:none; padding:8px 0;">Kategori</td>
                        <td style="font-weight:600; text-align:right; border:none; padding:8px 0;">{{ $product->category?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-muted); border:none; padding:8px 0;">Brand</td>
                        <td style="font-weight:600; text-align:right; border:none; padding:8px 0;">{{ $product->brand ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-muted); border:none; padding:8px 0;">Harga Beli</td>
                        <td style="font-weight:600; text-align:right; border:none; padding:8px 0;">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-muted); border:none; padding:8px 0;">Harga Jual</td>
                        <td style="font-weight:600; text-align:right; border:none; padding:8px 0; color:var(--color-success)">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-muted); border:none; padding:8px 0;">Status</td>
                        <td style="text-align:right; border:none; padding:8px 0;">
                            <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top:16px; display:flex; gap:10px;">
                <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-secondary btn-block" style="flex:1; text-align:center;">Edit Produk</a>
            </div>
        </div>
    </div>

    <!-- Varian & Stok -->
    <div class="card">
        <div class="card-header flex-between">
            <div class="card-title">Varian & Stok Barang</div>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU Varian</th>
                        <th>Warna</th>
                        <th>Ukuran</th>
                        <th style="text-align:center">Stok</th>
                        <th style="text-align:right">Harga Jual Override</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->variants as $variant)
                    <tr>
                        <td style="font-family:monospace; font-size:12px;">{{ $variant->sku_variant }}</td>
                        <td>{{ $variant->color ?? '-' }}</td>
                        <td>{{ $variant->size ?? '-' }}</td>
                        <td style="text-align:center">
                            <span class="badge {{ $variant->isLowStock() ? 'badge-danger' : 'badge-success' }}">{{ $variant->stock_qty }}</span>
                        </td>
                        <td style="text-align:right">
                            @if($variant->sell_price)
                                Rp {{ number_format($variant->sell_price, 0, ',', '.') }}
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Ikut Produk Utama</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:20px;">Tidak ada varian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
