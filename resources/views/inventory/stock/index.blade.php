@extends('layouts.app')

@section('title', 'Manajemen Stok')
@section('page-title', 'Stok & Inventori')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Stok & Inventori</h1>
        <p class="page-header-subtitle">Pantau nilai inventori, ketersediaan stok, dan riwayat pergerakan barang.</p>
    </div>
</div>

<div class="grid grid-3 mb-4">
    <div class="stat-card primary">
        <div class="stat-label">Total Nilai Inventori</div>
        <div class="stat-value">{{ number_format($stockValue ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:10px;color:var(--text-muted);font-family:monospace;margin-top:2px">Rp</div>
    </div>
    <div class="stat-card success" style="cursor:pointer" onclick="window.location='{{ route('inventory.stock.movements') }}'">
        <div class="stat-label">Riwayat Pergerakan Stok</div>
        <div style="margin-top:10px;color:var(--text-primary);font-size:13px;font-weight:600">Lihat Log →</div>
    </div>
    <div class="stat-card warning" style="cursor:pointer" onclick="window.location='{{ route('inventory.opname.index') }}'">
        <div class="stat-label">Stock Opname</div>
        <div style="margin-top:10px;color:var(--text-primary);font-size:13px;font-weight:600">Lakukan Opname →</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('inventory.stock.index') }}" method="GET" style="display:flex;gap:10px;width:100%">
        <div class="search-input" style="flex:1; max-width:400px;">
            <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" class="form-control" placeholder="Cari nama, SKU..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
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
                        <div style="font-weight:600">{{ $variant->product?->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $variant->product?->category?->name }}</div>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $variant->variant_label }}</span>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">{{ $variant->sku_variant }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $variant->isLowStock() ? 'badge-danger' : 'badge-success' }}" style="font-size:14px">
                            {{ $variant->stock_qty }}
                        </span>
                    </td>
                    <td class="currency">Rp {{ number_format($variant->effective_buy_price, 0, ',', '.') }}</td>
                    <td class="currency" style="font-weight:700">Rp {{ number_format($variant->effective_buy_price * $variant->stock_qty, 0, ',', '.') }}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="adjustStock({{ $variant->id }}, '{{ addslashes($variant->product?->name . ' - ' . $variant->variant_label) }}', {{ $variant->stock_qty }})">
                            Sesuaikan
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Tidak ada data stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($variants->hasPages())
    <div class="card-footer">{{ $variants->links() }}</div>
    @endif
</div>

<!-- Modal Sesuaikan Stok -->
<div class="modal-overlay" id="adjustModal" style="display:none">
    <div class="modal" style="max-width:400px">
        <div class="modal-header">
            <div class="modal-title">Sesuaikan Stok</div>
            <button onclick="document.getElementById('adjustModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="{{ route('inventory.stock.adjust') }}">
            @csrf
            <input type="hidden" name="product_variant_id" id="adjustVariantId">
            <div class="modal-body">
                <div style="margin-bottom:16px;font-weight:600" id="adjustProductName"></div>
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
