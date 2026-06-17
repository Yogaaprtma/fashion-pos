@extends('layouts.app')

@section('title', 'Laporan Diskon')
@section('page-title', 'Laporan Diskon')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Diskon</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box amber">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Diskon</h1>
                <p class="subtitle">Analisis pemberian diskon pada transaksi penjualan dalam periode tertentu.</p>
            </div>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($summary->total_trx ?? 0) }}</div>
                <div class="ph-stat-lbl">Transaksi Diskon</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:16px;color:#DC2626;">Rp {{ number_format($summary->total_discount ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Nilai Diskon</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($summary->avg_discount_pct ?? 0, 1) }}%</div>
                <div class="ph-stat-lbl">Rata-rata % Diskon</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar-enhanced">
    <form method="GET" action="{{ route('reports.discount') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;width:100%;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filter
        </button>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Daftar Transaksi Diskon
        </div>
        <span class="badge badge-warning">{{ $transactions->total() }} transaksi</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th style="text-align:center;">Diskon %</th>
                    <th style="text-align:right;">Nilai Diskon</th>
                    <th style="text-align:right;">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <a href="{{ route('pos.transaction.show', $trx) }}"
                           style="color:#4F46E5;font-family:monospace;font-weight:700;text-decoration:none;">
                            {{ $trx->invoice_number }}
                        </a>
                    </td>
                    <td style="font-size:13px;color:#6B7280;">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                    <td style="font-weight:600;">{{ $trx->cashierSession?->user?->name ?? '-' }}</td>
                    <td style="text-align:right;font-family:monospace;">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                    <td style="text-align:center;">
                        @if($trx->discount_percent > 0)
                            <span class="badge badge-warning">{{ $trx->discount_percent }}%</span>
                        @else
                            <span class="badge badge-secondary">Nominal</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:700;color:#DC2626;font-family:monospace;">
                        -Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;font-weight:800;font-family:monospace;color:#4F46E5;">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">🏷️</div>
                            <div class="empty-state-title">Tidak ada transaksi diskon</div>
                            <div class="empty-state-desc">Tidak ada transaksi dengan diskon pada periode ini.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer">{{ $transactions->links() }}</div>
    @endif
</div>

@endsection
