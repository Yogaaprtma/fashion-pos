@extends('layouts.app')

@section('title', 'Peringatan Stok Rendah')
@section('page-title', 'Peringatan Stok Rendah & Habis')

@section('content')

<div class="card mb-4" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2);">
    <div class="card-body">
        <h4 style="margin: 0 0 8px 0; color: #F59E0B; display:flex; align-items:center; gap:8px;">
            <span>⚠️</span> Barang-barang di bawah ini perlu segera di restock!
        </h4>
        <p style="margin:0; font-size: 13px; color: var(--text-muted);">
            Menampilkan produk yang jumlah stoknya telah mencapai atau di bawah batas minimum (min_stock).
        </p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Varian (SKU)</th>
                    <th style="text-align:center">Batas Minimum</th>
                    <th style="text-align:center">Stok Saat Ini</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($variants as $variant)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $variant->product?->name }}</div>
                    </td>
                    <td>{{ $variant->product?->category?->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-secondary">{{ $variant->variant_label }}</span>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">{{ $variant->sku_variant }}</div>
                    </td>
                    <td style="text-align:center; font-weight:600; color: var(--text-muted);">
                        {{ $variant->product?->min_stock ?? 5 }}
                    </td>
                    <td style="text-align:center;">
                        @if($variant->stock_qty <= 0)
                            <span class="badge badge-danger" style="font-size:14px">HABIS ({{ $variant->stock_qty }})</span>
                        @else
                            <span class="badge badge-warning" style="font-size:14px">{{ $variant->stock_qty }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('purchase.orders.create') }}?variant_id={{ $variant->id }}" class="btn btn-sm btn-primary">Buat PO</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <div style="font-size:30px; margin-bottom:10px;">🎉</div>
                        Semua stok produk dalam kondisi aman!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
