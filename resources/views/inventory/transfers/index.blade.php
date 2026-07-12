@extends('layouts.app')

@section('title', 'Mutasi Stok')
@section('page-title', 'Mutasi & Transfer Stok')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Inventori</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Mutasi Stok</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Mutasi Stok Antar Cabang</h1>
            <p class="page-header-subtitle">Transfer barang antar cabang/outlet dan pantau status pengiriman.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('inventory.transfers.create') }}" class="btn btn-primary">
                + Ajukan Mutasi
            </a>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Mutasi</th>
                    <th>Cabang Asal</th>
                    <th>Cabang Tujuan</th>
                    <th>Diajukan Oleh</th>
                    <th>Tanggal Diajukan</th>
                    <th>Status</th>
                    <th width="120" class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $t)
                <tr>
                    <td style="font-weight: 700; color: var(--color-primary);">{{ $t->transfer_number }}</td>
                    <td>{{ $t->fromBranch->name }}</td>
                    <td>{{ $t->toBranch->name }}</td>
                    <td>{{ $t->creator->name }}</td>
                    <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                    <td>
                        @if($t->status === 'draft')
                            <span class="badge badge-secondary">Draft</span>
                        @elseif($t->status === 'pending')
                            <span class="badge badge-warning">Menunggu Persetujuan</span>
                        @elseif($t->status === 'completed')
                            <span class="badge badge-success">Selesai (Stok Pindah)</span>
                        @else
                            <span class="badge badge-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('inventory.transfers.show', $t) }}" class="btn btn-sm btn-secondary">
                            Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px 0; color: var(--text-muted);">Belum ada riwayat mutasi stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transfers->hasPages())
    <div style="padding:16px 24px; border-top:1px solid var(--border)">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endsection
