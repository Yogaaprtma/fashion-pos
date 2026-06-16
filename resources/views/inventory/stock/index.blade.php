@extends('layouts.app')

@section('title', 'Manajemen Stok')
@section('page-title', 'Stok & Inventori')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Inventori</span>
        <span class="sep">›</span>
        <span>Manajemen Stok</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-left" style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box emerald">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h1>Manajemen Stok</h1>
                <p class="subtitle">Pantau nilai inventori, ketersediaan stok, dan riwayat pergerakan barang.</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;">
            <a href="{{ route('inventory.stock.movements') }}" class="btn btn-secondary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Pergerakan
            </a>
            <a href="{{ route('inventory.opname.index') }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Opname
            </a>
        </div>
    </div>

    {{-- Stat strip --}}
    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">Rp {{ number_format($stockValue ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Nilai Inventori</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ $variants->total() ?? 0 }}</div>
                <div class="ph-stat-lbl">Total Varian</div>
            </div>
        </div>
        @php $lowCount = $variants->filter(fn($v)=>$v->isLowStock())->count(); @endphp
        <div class="ph-stat">
            <div class="ph-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:{{ $lowCount > 0 ? '#DC2626' : '#111827' }}">{{ $lowCount }}</div>
                <div class="ph-stat-lbl">Stok Rendah</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar-enhanced">
    <form action="{{ route('inventory.stock.index') }}" method="GET" style="display:flex;gap:10px;width:100%;align-items:flex-end;">
        <div class="form-group" style="flex:1;max-width:420px;margin:0;">
            <label class="form-label">Cari Produk</label>
            <div class="search-input">
                <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" class="form-control" placeholder="Nama produk, SKU..." value="{{ request('search') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
        </button>
        @if(request('search'))
        <a href="{{ route('inventory.stock.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Daftar Stok Varian
        </div>
        <span class="badge badge-primary">{{ $variants->total() }} item</span>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk & Kategori</th>
                    <th>Varian (SKU)</th>
                    <th>Stok Tersedia</th>
                    <th>Harga Beli</th>
                    <th>Total Nilai</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($variants as $variant)
                <tr>
                    <td>
                        <div style="font-weight:700;color:#111827;">{{ $variant->product?->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ $variant->product?->category?->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $variant->variant_label }}</span>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;font-family:monospace;">{{ $variant->sku_variant }}</div>
                    </td>
                    <td>
                        @if($variant->isLowStock())
                        <span class="badge badge-danger" style="font-size:14px;font-weight:800;">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            {{ $variant->stock_qty }}
                        </span>
                        @else
                        <span class="badge badge-success" style="font-size:14px;font-weight:800;">{{ $variant->stock_qty }}</span>
                        @endif
                    </td>
                    <td class="currency">Rp {{ number_format($variant->effective_buy_price, 0, ',', '.') }}</td>
                    <td class="currency" style="font-weight:700;color:#4F46E5;">Rp {{ number_format($variant->effective_buy_price * $variant->stock_qty, 0, ',', '.') }}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="adjustStock({{ $variant->id }}, '{{ addslashes($variant->product?->name . ' - ' . $variant->variant_label) }}', {{ $variant->stock_qty }})">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Sesuaikan
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <div class="empty-state-title">Tidak ada data stok</div>
                            <div class="empty-state-desc">Belum ada produk yang terdaftar di inventori.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($variants->hasPages())
    <div class="card-footer">{{ $variants->links() }}</div>
    @endif
</div>

{{-- Modal Sesuaikan Stok --}}
<div class="modal-overlay" id="adjustModal" style="display:none">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <div class="modal-title">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#4F46E5;margin-right:6px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Sesuaikan Stok
            </div>
            <button onclick="document.getElementById('adjustModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="{{ route('inventory.stock.adjust') }}">
            @csrf
            <input type="hidden" name="product_variant_id" id="adjustVariantId">
            <div class="modal-body">
                <div style="margin-bottom:16px;font-weight:700;color:#111827;font-size:14px;" id="adjustProductName"></div>
                <div class="form-group">
                    <label class="form-label">Stok Baru (Hasil penyesuaian)</label>
                    <input type="number" name="new_quantity" id="adjustNewQty" class="form-control" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Alasan / Catatan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Barang rusak, hilang, dsb..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('adjustModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-warning">Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>

<script>
    function adjustStock(id, name, currentQty) {
        document.getElementById('adjustVariantId').value = id;
        document.getElementById('adjustProductName').textContent = name;
        document.getElementById('adjustNewQty').value = currentQty;
        document.getElementById('adjustModal').style.display = 'flex';
    }
</script>
@endsection
