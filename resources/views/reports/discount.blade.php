@extends('layouts.app')

@section('title', 'Laporan Diskon')
@section('page-title', 'Laporan Diskon')

@section('content')

<!-- Filter -->
<div class="card mb-4">
    <div class="card-header">
        <form method="GET" action="{{ route('reports.discount') }}" style="display:flex;gap:12px;align-items:flex-end;">
            <div>
                <label class="form-label" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid-3 mb-6">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">🏷️</div>
        <div class="kpi-content">
            <div class="kpi-label">Total Transaksi Diskon</div>
            <div class="kpi-value">{{ number_format($summary->total_trx ?? 0) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:rgba(239,68,68,.1);color:#ef4444;">💸</div>
        <div class="kpi-content">
            <div class="kpi-label">Total Nilai Diskon</div>
            <div class="kpi-value">Rp {{ number_format($summary->total_discount ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">📊</div>
        <div class="kpi-content">
            <div class="kpi-label">Rata-rata % Diskon</div>
            <div class="kpi-value">{{ number_format($summary->avg_discount_pct ?? 0, 1) }}%</div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
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
                           style="color:var(--color-primary);font-family:monospace;font-weight:600;">
                            {{ $trx->invoice_number }}
                        </a>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                    <td>{{ $trx->cashierSession?->user?->name ?? '-' }}</td>
                    <td style="text-align:right;">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                    <td style="text-align:center;">
                        @if($trx->discount_percent > 0)
                            <span class="badge badge-warning">{{ $trx->discount_percent }}%</span>
                        @else
                            <span class="badge badge-secondary">Nominal</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:600;color:var(--color-danger);">
                        - Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;font-weight:700;">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <div style="font-size:36px;margin-bottom:10px;">🏷️</div>
                        Tidak ada transaksi diskon pada periode ini.
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
