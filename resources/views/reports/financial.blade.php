@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Laba Rugi (P&L)')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Keuangan</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box emerald">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Laba Rugi (P&L)</h1>
                <p class="subtitle">Ringkasan keuangan otomatis berdasarkan transaksi penjualan, HPP, retur, dan beban operasional.</p>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <a href="{{ route('reports.financial.export-pdf', request()->all()) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-danger);border-color:var(--color-danger-border);">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Cetak PDF
            </a>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:15px;">Rp {{ number_format($financial['net_sales'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Net Sales</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:15px;">Rp {{ number_format($financial['gross_profit'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Laba Kotor</div>
            </div>
        </div>
        <div class="ph-stat" style="{{ ($financial['net_profit'] ?? 0) >= 0 ? 'border-color:#A7F3D0' : 'border-color:#FECACA' }}">
            <div class="ph-stat-icon {{ ($financial['net_profit'] ?? 0) >= 0 ? 'green' : 'red' }}">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ ($financial['net_profit'] ?? 0) >= 0 ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:15px;color:{{ ($financial['net_profit'] ?? 0) >= 0 ? '#059669' : '#DC2626' }}">Rp {{ number_format($financial['net_profit'] ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Laba Bersih</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ $financial['margin_percentage'] ?? 0 }}%</div>
                <div class="ph-stat-lbl">Gross Margin</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar-enhanced">
    <form action="{{ route('reports.financial') }}" method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;flex:1;min-width:120px;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-control">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:150px;">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-control">
                <option value="">Semua Bulan (Tahunan)</option>
                @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $name)
                    <option value="{{ $num }}" {{ request('month', date('m')) == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Tampilkan
        </button>
    </form>
</div>

<div class="grid grid-2" style="gap:24px; align-items:start;">
    {{-- Statement P&L --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="section-title-dot"></span>
                Income Statement
            </div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table" style="font-size:14px;">
                <tbody>
                    <tr style="background:#F8FAFC">
                        <td style="font-weight:700;color:#374151;font-size:11px;letter-spacing:0.6px;text-transform:uppercase;" colspan="2">PENDAPATAN (REVENUE)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Penjualan Kotor</td>
                        <td style="text-align:right;font-weight:600;">Rp {{ number_format($financial['gross_sales'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px; color:var(--color-danger)">Retur Penjualan</td>
                        <td style="text-align:right; color:var(--color-danger)">(Rp {{ number_format($financial['returns'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px; color:var(--color-danger)">Diskon Diberikan</td>
                        <td style="text-align:right; color:var(--color-danger)">(Rp {{ number_format($financial['discounts'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr style="font-weight:700;background:#F0FDF4;">
                        <td style="color:#065F46;">Penjualan Bersih (Net Sales)</td>
                        <td style="text-align:right;color:#065F46;">Rp {{ number_format($financial['net_sales'] ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <tr style="background:#F8FAFC">
                        <td style="font-weight:700;color:#374151;font-size:11px;letter-spacing:0.6px;text-transform:uppercase;" colspan="2">HARGA POKOK PENJUALAN (COGS)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Biaya Pembelian Barang Terjual</td>
                        <td style="text-align:right;color:var(--color-danger);">(Rp {{ number_format($financial['cogs'] ?? 0, 0, ',', '.') }})</td>
                    </tr>

                    <tr style="font-weight:800; background:rgba(16, 185, 129, 0.08);">
                        <td style="color:var(--color-success);font-size:15px;">LABA KOTOR (GROSS PROFIT)</td>
                        <td style="text-align:right; color:var(--color-success);font-size:15px;">Rp {{ number_format($financial['gross_profit'] ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <tr style="background:#F8FAFC">
                        <td style="font-weight:700;color:#374151;font-size:11px;letter-spacing:0.6px;text-transform:uppercase;" colspan="2">BEBAN OPERASIONAL (OPEX)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Depresiasi Aset</td>
                        <td style="text-align:right;color:var(--color-danger);">(Rp {{ number_format($financial['depreciation'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Selisih Opname (Shrinkage)</td>
                        <td style="text-align:right;color:var(--color-danger);">(Rp {{ number_format($financial['shrinkage'] ?? 0, 0, ',', '.') }})</td>
                    </tr>

                    <tr style="font-weight:800; font-size:16px; background:linear-gradient(135deg,#4F46E5,#2563EB); color:white;">
                        <td style="border:none;border-radius:0 0 0 0;">LABA BERSIH (NET PROFIT)</td>
                        <td style="text-align:right; border:none;">Rp {{ number_format($financial['net_profit'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Analytics --}}
    <div>
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">
                    <span class="section-title-dot"></span>
                    Gross Profit Margin
                </div>
            </div>
            <div class="card-body" style="text-align:center;padding:28px;">
                <div style="font-family:'Outfit',sans-serif;font-size:52px;font-weight:900;letter-spacing:-2px;color:#4F46E5;line-height:1;">{{ $financial['margin_percentage'] ?? 0 }}%</div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:8px;">Laba Kotor / Penjualan Bersih</div>
                <div class="progress" style="margin-top:16px;">
                    <div class="progress-bar" style="width:{{ min($financial['margin_percentage'] ?? 0, 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="section-title-dot"></span>
                    Catatan Finansial
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="background:#F0F9FF;border:1px solid #BAE6FD;border-radius:10px;padding:12px 14px;font-size:13px;color:#0369A1;line-height:1.6;">
                        💡 Laporan dihitung otomatis berdasarkan transaksi penjualan (dikurangi retur & diskon) dan HPP saat transaksi terjadi.
                    </div>
                    <div style="background:#F8FAFC;border:1px solid #E5E7EB;border-radius:10px;padding:12px 14px;font-size:13px;color:#6B7280;line-height:1.6;">
                        Beban operasional tambahan seperti Gaji Karyawan, Listrik, dan Air dapat ditambahkan pada modul Akuntansi lanjutan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
