@extends('layouts.app')

@section('title', 'Peringatan Stok Menipis')
@section('page-title', 'Peringatan Stok Menipis')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">⚠️ Peringatan Stok Menipis</h1>
        <p class="page-header-subtitle">Daftar produk yang stoknya sudah di bawah atau mencapai batas minimum. Segera lakukan pengadaan.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Buat Purchase Order
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:20px; border-top:3px solid #EF4444;">
        <div style="font-size:30px; font-weight:900; color:#EF4444;">{{ $outOfStockCount }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Habis (Stok = 0)</div>
    </div>
    <div class="card" style="padding:20px; border-top:3px solid #F59E0B;">
        <div style="font-size:30px; font-weight:900; color:#F59E0B;">{{ $criticalCount }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Kritis (Stok 1–3)</div>
    </div>
    <div class="card" style="padding:20px; border-top:3px solid #3B82F6;">
        <div style="font-size:30px; font-weight:900; color:#3B82F6;">{{ $lowCount }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Rendah (Stok 4–10)</div>
    </div>
    <div class="card" style="padding:20px; border-top:3px solid var(--color-primary);">
        <div style="font-size:30px; font-weight:900; color:var(--color-primary);">{{ $lowStockProducts->count() }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Total Produk Perlu Restock</div>
    </div>
</div>

{{-- Product List --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        @if($lowStockProducts->isEmpty())
        <div class="empty-state" style="padding:60px 20px;">
            <div class="empty-state-icon">✅</div>
            <div class="empty-state-title">Semua Stok Aman!</div>
            <div class="empty-state-desc">Tidak ada produk yang stoknya di bawah batas minimum saat ini. Stok terpantau dengan baik.</div>
        </div>
        @else
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Min. Stok</th>
                    <th>Varian & Stok</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                @php
                    $totalStock = $product->variants->sum('stock');
                    $isOut = $totalStock == 0;
                    $isCritical = $totalStock > 0 && $totalStock <= 3;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;">{{ $product->name }}</div>
                        <div style="font-size:12px; color:var(--text-muted); font-family:monospace;">{{ $product->sku }}</div>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $product->category?->name ?? '-' }}</span>
                    </td>
                    <td style="text-align:center; font-weight:600;">{{ $product->min_stock }}</td>
                    <td>
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach($product->variants->sortBy('stock') as $variant)
                            @php
                                $stockQty = $variant->stock ?? $variant->stock_qty ?? 0;
                                $color = $stockQty == 0 ? '#EF4444' : ($stockQty <= 3 ? '#F59E0B' : '#3B82F6');
                            @endphp
                            <span style="display:inline-flex; align-items:center; gap:4px; background:{{ $color }}18; border:1px solid {{ $color }}44; border-radius:20px; padding:2px 10px; font-size:12px; font-weight:600; color:{{ $color }};">
                                {{ $variant->size }}/{{ $variant->color }} &nbsp;·&nbsp; {{ $stockQty }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td style="text-align:center;">
                        @if($isOut)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($isCritical)
                            <span class="badge badge-warning">Kritis</span>
                        @else
                            <span class="badge badge-info">Rendah</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-sm btn-ghost">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
