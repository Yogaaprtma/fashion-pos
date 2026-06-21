@extends('layouts.app')
@section('title', 'Laporan Retur & Void')
@section('page-title', 'Laporan Retur & Void')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Retur & Void</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box rose">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Retur & Void Transaksi</h1>
                <p class="subtitle">Daftar transaksi barang kembali dan transaksi yang dibatalkan oleh kasir.</p>
            </div>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat" style="border-color:#FECACA;background:#FEF2F2;">
            <div class="ph-stat-icon red" style="background:rgba(239,68,68,0.15);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:#DC2626;">{{ $returns->count() }} Retur</div>
                <div class="ph-stat-lbl">Total Refund: Rp {{ number_format($returns->sum('total_refund'), 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="ph-stat" style="border-color:#FDE68A;background:#FFFBEB;">
            <div class="ph-stat-icon amber" style="background:rgba(245,158,11,0.15);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:#D97706;">{{ $voids->count() }} Void</div>
                <div class="ph-stat-lbl">Total Nilai: Rp {{ number_format($voids->sum('grand_total'), 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar-enhanced">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;width:100%;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filter Data
        </button>
    </form>
</div>

<div class="grid grid-2" style="gap:24px; align-items:start;">
    {{-- Returns Table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="section-title-dot"></span>
                Daftar Retur Barang
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Retur / Trx Asal</th>
                        <th>Alasan & Status</th>
                        <th style="text-align:right;">Total Refund</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $r)
                    <tr>
                        <td>
                            <div style="font-weight:700;font-family:monospace;color:#111827;">{{ $r->return_number }}</div>
                            <div style="font-size:11px;color:#6B7280;margin-top:4px;">Asal: {{ $r->transaction?->invoice_number }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;margin-bottom:6px;">{{ Str::limit($r->reason, 40) }}</div>
                            @if($r->status === 'approved') <span class="badge badge-success" style="font-size:10px;">Approved</span>
                            @elseif($r->status === 'rejected') <span class="badge badge-danger" style="font-size:10px;">Ditolak</span>
                            @else <span class="badge badge-warning" style="font-size:10px;">Pending</span> @endif
                        </td>
                        <td style="text-align:right;font-family:monospace;font-weight:700;color:#DC2626;">
                            -Rp {{ number_format($r->total_refund, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:30px 20px;">
                                <div class="empty-state-icon">✅</div>
                                <div class="empty-state-title">Tidak ada retur</div>
                                <div class="empty-state-desc">Aman, tidak ada barang yang diretur.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Voids Table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="section-title-dot"></span>
                Daftar Transaksi Void
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Kasir & Alasan</th>
                        <th style="text-align:right;">Total Batal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($voids as $v)
                    <tr>
                        <td>
                            <div style="font-weight:700;font-family:monospace;color:#111827;">{{ $v->invoice_number }}</div>
                            <div style="font-size:11px;color:#6B7280;margin-top:4px;">{{ $v->voided_at?->format('d/m/Y H:i') ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-size:12px;font-weight:600;color:#111827;margin-bottom:2px;">{{ $v->cashierSession?->user?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#6B7280;">{{ Str::limit($v->void_reason, 40) }}</div>
                        </td>
                        <td style="text-align:right;font-family:monospace;font-weight:700;color:#D97706;">
                            Rp {{ number_format($v->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:30px 20px;">
                                <div class="empty-state-icon">✅</div>
                                <div class="empty-state-title">Tidak ada transaksi batal</div>
                                <div class="empty-state-desc">Aman, tidak ada transaksi yang di-void.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
