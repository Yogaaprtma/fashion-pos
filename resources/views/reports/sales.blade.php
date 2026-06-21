@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Penjualan</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box indigo">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Penjualan</h1>
                <p class="subtitle">Analisis data penjualan, produk terlaris, dan ringkasan pendapatan per periode.</p>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <a href="{{ route('reports.sales.export-pdf', request()->all()) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-danger);border-color:var(--color-danger-border);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Cetak PDF
            </a>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:16px;">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Pendapatan</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($summary['transaction_count'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Transaksi</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($summary['items_sold'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Barang Terjual (pcs)</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar-enhanced">
    <form action="{{ route('reports.sales') }}" method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;width:100%">
        <div class="form-group" style="margin:0;flex:1;min-width:150px">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate) }}">
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:150px">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}">
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:150px">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filter
        </button>
    </form>
</div>

{{-- Tables --}}
<div class="grid grid-2" style="gap:20px">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="section-title-dot"></span>
                Penjualan per Hari
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Transaksi</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailySales ?? [] as $day)
                    <tr>
                        <td style="font-weight:600;">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                        <td><span class="badge badge-primary">{{ $day->count }}</span></td>
                        <td class="currency" style="color:#4F46E5;font-weight:700;">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:30px 20px;">
                                <div class="empty-state-icon">📊</div>
                                <div class="empty-state-title">Tidak ada data</div>
                                <div class="empty-state-desc">Tidak ada transaksi pada periode ini.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="section-title-dot"></span>
                Produk Terlaris
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk & Varian</th>
                        <th>Terjual</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts ?? [] as $item)
                    <tr>
                        <td>
                            <div style="font-weight:700;color:#111827;">{{ $item->product_name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $item->variant_label }}</div>
                        </td>
                        <td><span class="badge badge-success">{{ $item->total_qty }} pcs</span></td>
                        <td class="currency" style="color:#4F46E5;font-weight:700;">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:30px 20px;">
                                <div class="empty-state-icon">🏆</div>
                                <div class="empty-state-title">Tidak ada data</div>
                                <div class="empty-state-desc">Tidak ada produk terjual pada periode ini.</div>
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
