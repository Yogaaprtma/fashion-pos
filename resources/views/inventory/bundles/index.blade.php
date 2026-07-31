@extends('layouts.app')

@section('title', 'Produk Paket / Bundling')
@section('page-title', 'Produk Paket / Bundling')

@section('content')

<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Inventori</span>
        <span class="sep">›</span>
        <span>Produk Paket</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box indigo">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <h1>Produk Paket / Bundling</h1>
                <p class="subtitle">Buat paket produk (misal: Setelan Formal Kemeja + Celana). Stok masing-masing item otomatis terpotong saat terjual.</p>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <a href="{{ route('inventory.bundles.create') }}" class="btn btn-primary">
                + Buat Paket Baru
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>SKU</th>
                        <th>Isi Paket</th>
                        <th>Harga Normal</th>
                        <th>Harga Paket</th>
                        <th>Diskon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bundles as $bundle)
                    <tr>
                        <td>
                            <div style="font-weight:700;">{{ $bundle->name }}</div>
                            @if($bundle->description)
                            <div style="font-size:11px;color:var(--text-muted);">{{ Str::limit($bundle->description, 50) }}</div>
                            @endif
                        </td>
                        <td><code style="font-size:11px;">{{ $bundle->sku }}</code></td>
                        <td>
                            @foreach($bundle->items as $item)
                            <div style="font-size:12px;">
                                {{ $item->quantity }}× {{ $item->productVariant->product->name }}
                                <span style="color:var(--text-muted);">({{ $item->productVariant->variant_label }})</span>
                            </div>
                            @endforeach
                        </td>
                        <td style="text-decoration:line-through;color:var(--text-muted);">
                            Rp {{ number_format($bundle->normal_total, 0, ',', '.') }}
                        </td>
                        <td><strong>Rp {{ number_format($bundle->bundle_price, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($bundle->discount_percent > 0)
                            <span class="badge badge-success">{{ $bundle->discount_percent }}% OFF</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($bundle->is_active)
                            <span class="badge badge-success">Aktif</span>
                            @else
                            <span class="badge badge-secondary">Non-aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('inventory.bundles.edit', $bundle) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('inventory.bundles.destroy', $bundle) }}"
                                    onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada paket produk. Klik "+ Buat Paket Baru" untuk memulai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bundles->hasPages())
    <div class="card-footer">{{ $bundles->links() }}</div>
    @endif
</div>

@endsection
