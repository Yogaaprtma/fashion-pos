@extends('layouts.app')
@section('title', 'Laporan per Kategori')
@section('page-title', 'Penjualan Kategori')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Per Kategori</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box cyan">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Penjualan per Kategori</h1>
                <p class="subtitle">Analisis performa produk per kategori, mencakup kuantitas terjual, omzet, dan margin profitabilitas.</p>
            </div>
        </div>
    </div>
    
    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($data->sum('total_qty') ?? 0) }} pcs</div>
                <div class="ph-stat-lbl">Total Qty Terjual</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">Rp {{ number_format($data->sum('total_sales') ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Penjualan</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:#059669;">Rp {{ number_format($data->sum('total_profit') ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Estimasi Profit</div>
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
            Terapkan Filter
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Data Penjualan per Kategori
        </div>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kategori Produk</th>
                    <th style="text-align:center;">Total Qty Terjual</th>
                    <th style="text-align:right;">Total Penjualan</th>
                    <th style="text-align:right;">Estimasi Profit</th>
                    <th style="text-align:right;">Margin (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $cat => $d)
                <tr>
                    <td style="font-weight:700;color:#111827;">{{ $cat }}</td>
                    <td style="text-align:center;"><span class="badge badge-secondary">{{ number_format($d['total_qty']) }} pcs</span></td>
                    <td style="text-align:right;font-family:monospace;font-weight:600;font-size:14px;">Rp {{ number_format($d['total_sales'], 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;font-weight:700;color:#059669;font-size:14px;">Rp {{ number_format($d['total_profit'], 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:700;">
                        <span class="badge badge-success">{{ $d['total_sales'] > 0 ? number_format(($d['total_profit'] / $d['total_sales']) * 100, 1) : 0 }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">🏷️</div>
                            <div class="empty-state-title">Data tidak ditemukan</div>
                            <div class="empty-state-desc">Belum ada transaksi pada periode ini.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($data->count())
            <tfoot>
                <tr style="font-weight:800;background:#F0F4FF;color:#4F46E5;font-size:13.5px;">
                    <td>TOTAL KESELURUHAN</td>
                    <td style="text-align:center;">{{ number_format($data->sum('total_qty')) }} pcs</td>
                    <td style="text-align:right;font-family:monospace;font-size:15px;">Rp {{ number_format($data->sum('total_sales'), 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;color:#059669;font-size:15px;">Rp {{ number_format($data->sum('total_profit'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
