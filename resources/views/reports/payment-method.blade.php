@extends('layouts.app')
@section('title', 'Laporan per Metode Pembayaran')
@section('page-title', 'Laporan Pembayaran')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Pembayaran</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box blue">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Metode Pembayaran</h1>
                <p class="subtitle">Analisis preferensi metode pembayaran pelanggan dan total penerimaan dana per metode.</p>
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

{{-- Overview Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;margin-bottom:24px;">
    @foreach($data as $method => $d)
    <div class="card" style="transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 28px rgba(79,70,229,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div style="width:48px;height:48px;border-radius:12px;background:{{ $method == 'Cash' ? '#ECFDF5' : ($method == 'Qris' ? '#EFF6FF' : '#F5F3FF') }};display:flex;align-items:center;justify-content:center;font-size:24px;">
                    {{ $method == 'Cash' ? '💵' : ($method == 'Qris' ? '📱' : '💳') }}
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:800;font-size:16px;color:#111827;letter-spacing:-0.3px;">{{ strtoupper($method) }}</div>
                    <div style="font-size:12px;color:#6B7280;margin-top:2px;">{{ number_format($d['total_count']) }} transaksi</div>
                </div>
            </div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6B7280;margin-bottom:4px;letter-spacing:0.5px;">Total Nominal</div>
            <div style="font-family:'Outfit',sans-serif;font-size:26px;font-weight:900;color:#4F46E5;line-height:1;letter-spacing:-0.5px;">
                Rp {{ number_format($d['total_amount'], 0, ',', '.') }}
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Detail per Metode Pembayaran
        </div>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Metode Pembayaran</th>
                    <th style="text-align:center;">Jumlah Transaksi</th>
                    <th style="text-align:right;">Total Nominal</th>
                    <th style="text-align:right;">Rata-rata per Trx</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $method => $d)
                <tr>
                    <td style="font-weight:700;color:#111827;">{{ $method }}</td>
                    <td style="text-align:center;"><span class="badge badge-primary">{{ number_format($d['total_count']) }}</span></td>
                    <td style="text-align:right;font-family:monospace;font-weight:700;font-size:15px;color:#059669;">Rp {{ number_format($d['total_amount'], 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;color:#4B5563;">Rp {{ $d['total_count'] > 0 ? number_format($d['total_amount'] / $d['total_count'], 0, ',', '.') : 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <div class="empty-state-icon">💳</div>
                            <div class="empty-state-title">Data tidak ditemukan</div>
                            <div class="empty-state-desc">Belum ada transaksi pembayaran pada periode ini.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
