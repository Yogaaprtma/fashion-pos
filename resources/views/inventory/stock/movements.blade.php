@extends('layouts.app')

@section('title', 'Pergerakan Stok')
@section('page-title', 'Riwayat Pergerakan Stok')

@section('content')

<div class="card">
    <div class="card-header">
        <div style="font-weight: 500; font-size: 14px; color: var(--text-muted)">Semua aktivitas keluar masuk barang tercatat di sini.</div>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Produk & Varian</th>
                    <th>Tipe Aktivitas</th>
                    <th style="text-align:right">Qty</th>
                    <th>Stok Akhir</th>
                    <th>User / Kasir</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div style="font-weight:600">{{ $mov->productVariant?->product?->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $mov->productVariant?->variant_label }}</div>
                    </td>
                    <td>
                        @php
                            $typeMap = [
                                'in' => ['Masuk', 'badge-success'],
                                'out' => ['Keluar', 'badge-danger'],
                                'adjustment' => ['Penyesuaian', 'badge-warning'],
                                'return' => ['Retur', 'badge-primary'],
                                'opname' => ['Opname', 'badge-secondary'],
                            ];
                            $c = $typeMap[$mov->type] ?? ['Unknown', 'badge-secondary'];
                        @endphp
                        <span class="badge {{ $c[1] }}">{{ $c[0] }}</span>
                    </td>
                    <td style="text-align:right; font-weight:700; color:{{ $mov->quantity > 0 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                        {{ $mov->quantity > 0 ? '+'.$mov->quantity : $mov->quantity }}
                    </td>
                    <td><span class="badge badge-secondary">{{ $mov->stock_after }}</span></td>
                    <td>{{ $mov->user?->name ?? 'Sistem' }}</td>
                    <td style="font-size:12px;color:var(--text-muted);max-width:200px;">
                        {{ $mov->notes ?? '-' }} 
                        @if($mov->reference_type)
                            (Ref: {{ $mov->reference_type }})
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:30px">Belum ada riwayat pergerakan stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="card-footer">{{ $movements->links() }}</div>
    @endif
</div>

@endsection
