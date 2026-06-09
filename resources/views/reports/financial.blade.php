@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Laba Rugi (P&L)')

@section('content')

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body" style="padding:16px">
        <form action="{{ route('reports.financial') }}" method="GET" style="display:flex;gap:12px;align-items:flex-end;">
            <div class="form-group" style="margin:0;flex:1;">
                <label class="form-label" style="font-size:11px">Tahun</label>
                <select name="year" class="form-control">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;">
                <label class="form-label" style="font-size:11px">Bulan</label>
                <select name="month" class="form-control">
                    <option value="">Semua Bulan (Tahunan)</option>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $name)
                        <option value="{{ $num }}" {{ request('month', date('m')) == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('reports.financial.export-pdf', request()->all()) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-danger)">
                    📄 Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-2" style="gap:24px; align-items:start;">
    <!-- Statement P&L -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Income Statement</div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table" style="font-size:14px;">
                <tbody>
                    <!-- Omzet -->
                    <tr style="background:var(--bg-elevated)">
                        <td style="font-weight:600;" colspan="2">PENDAPATAN (REVENUE)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Penjualan Kotor</td>
                        <td style="text-align:right;">Rp {{ number_format($financial['gross_sales'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px; color:var(--color-danger)">Retur Penjualan</td>
                        <td style="text-align:right; color:var(--color-danger)">(Rp {{ number_format($financial['returns'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px; color:var(--color-danger)">Diskon Diberikan</td>
                        <td style="text-align:right; color:var(--color-danger)">(Rp {{ number_format($financial['discounts'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr style="font-weight:700;">
                        <td>Penjualan Bersih (Net Sales)</td>
                        <td style="text-align:right;">Rp {{ number_format($financial['net_sales'] ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <!-- HPP -->
                    <tr style="background:var(--bg-elevated)">
                        <td style="font-weight:600;" colspan="2">HARGA POKOK PENJUALAN (COGS)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Biaya Pembelian Barang Terjual</td>
                        <td style="text-align:right;">(Rp {{ number_format($financial['cogs'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    
                    <!-- Laba Kotor -->
                    <tr style="font-weight:800; font-size:16px; background:rgba(16, 185, 129, 0.1);">
                        <td style="color:var(--color-success)">LABA KOTOR (GROSS PROFIT)</td>
                        <td style="text-align:right; color:var(--color-success)">Rp {{ number_format($financial['gross_profit'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    
                    <!-- Beban Operasional -->
                    <tr style="background:var(--bg-elevated)">
                        <td style="font-weight:600;" colspan="2">BEBAN OPERASIONAL (OPEX)</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Depresiasi Aset</td>
                        <td style="text-align:right;">(Rp {{ number_format($financial['depreciation'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    <tr>
                        <td style="padding-left:24px;">Selisih Opname (Shrinkage)</td>
                        <td style="text-align:right;">(Rp {{ number_format($financial['shrinkage'] ?? 0, 0, ',', '.') }})</td>
                    </tr>
                    
                    <!-- Laba Bersih -->
                    <tr style="font-weight:800; font-size:18px; background:var(--color-primary); color:white;">
                        <td style="border:none">LABA BERSIH (NET PROFIT)</td>
                        <td style="text-align:right; border:none">Rp {{ number_format($financial['net_profit'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Margin & Analytics -->
    <div>
        <div class="stat-card primary mb-4">
            <div class="stat-label">Gross Profit Margin</div>
            <div class="stat-value">{{ $financial['margin_percentage'] ?? 0 }}%</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">(Laba Kotor / Penjualan Bersih)</div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">Catatan Finansial</div>
            </div>
            <div class="card-body">
                <p style="font-size:13px; color:var(--text-muted); line-height:1.6;">
                    Laporan Laba Rugi ini dihitung secara otomatis berdasarkan transaksi penjualan (dikurangi retur & diskon) dan HPP (Harga Pokok Penjualan) barang saat transaksi terjadi.
                </p>
                <p style="font-size:13px; color:var(--text-muted); line-height:1.6;">
                    Beban operasional tambahan seperti Gaji Karyawan, Listrik, dan Air dapat ditambahkan pada modul Akuntansi lanjutan (jika tersedia).
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
