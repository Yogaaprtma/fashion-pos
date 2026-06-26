@extends('layouts.app')

@section('title', 'Manajemen Promosi')
@section('page-title', 'Promosi & Voucher')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Inventori</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Promosi & Voucher</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Promosi & Voucher Toko</h1>
            <p class="page-header-subtitle">Kelola kupon diskon, voucher, dan event promosi otomatis untuk POS Anda.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('inventory.promotions.create') }}" class="btn btn-primary">
                + Tambah Promosi
            </a>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="card-header flex-between" style="padding: 16px 24px; border-bottom: 1px solid var(--border)">
        <div class="card-title" style="font-size:16px;">Daftar Promosi Aktif</div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Promo</th>
                    <th>Kode Kupon</th>
                    <th>Tipe Promo</th>
                    <th>Nilai</th>
                    <th>Persyaratan</th>
                    <th>Masa Berlaku</th>
                    <th>Status</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $promo->name }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Target: {{ strtoupper($promo->target_type) }}</div>
                    </td>
                    <td>
                        @if($promo->code)
                            <span class="badge" style="background: rgba(79, 70, 229, 0.1); color: var(--color-primary); font-family: monospace; font-size: 12px; font-weight: 600; padding: 4px 8px; border: 1px dashed rgba(79, 70, 229, 0.3)">
                                {{ $promo->code }}
                            </span>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;">Otomatis (Tanpa Kupon)</span>
                        @endif
                    </td>
                    <td>
                        @if($promo->type === 'discount_percent')
                            Diskon Persentase
                        @elseif($promo->type === 'discount_fixed')
                            Diskon Nominal
                        @elseif($promo->type === 'bogo')
                            Beli 1 Gratis 1 (BOGO)
                        @else
                            Paket Bundling
                        @endif
                    </td>
                    <td style="font-weight: 600;">
                        @if($promo->type === 'discount_percent')
                            {{ (float)$promo->value }}%
                        @elseif($promo->type === 'discount_fixed')
                            Rp {{ number_format($promo->value, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($promo->min_requirement_type === 'none')
                            <span style="color: var(--text-muted)">Tidak ada</span>
                        @elseif($promo->min_requirement_type === 'min_spend')
                            Min. Belanja Rp {{ number_format($promo->min_requirement_value, 0, ',', '.') }}
                        @else
                            Min. Qty {{ (int)$promo->min_requirement_value }} Pcs
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 12px; font-weight:500;">{{ $promo->start_date->format('d M Y') }} - {{ $promo->end_date->format('d M Y') }}</div>
                        <div style="font-size: 10px; color: var(--text-muted);">{{ $promo->start_date->format('H:i') }} - {{ $promo->end_date->format('H:i') }}</div>
                    </td>
                    <td>
                        @if($promo->isValid())
                            <span class="badge badge-success">Aktif</span>
                        @elseif(!$promo->is_active)
                            <span class="badge badge-secondary">Nonaktif</span>
                        @else
                            <span class="badge badge-danger">Kedaluwarsa / Habis</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <a href="{{ route('inventory.promotions.edit', $promo) }}" class="btn btn-sm btn-ghost btn-icon" title="Edit">
                                📝
                            </a>
                            <form action="{{ route('inventory.promotions.destroy', $promo) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promosi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost btn-icon text-danger" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 60px 0; color: var(--text-muted);">
                        <div style="font-size: 24px; margin-bottom: 8px;">🎟️</div>
                        <div>Belum ada event promosi atau voucher yang terdaftar.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($promotions->hasPages())
    <div style="padding: 16px 24px; border-top: 1px solid var(--border)">
        {{ $promotions->links() }}
    </div>
    @endif
</div>
@endsection
