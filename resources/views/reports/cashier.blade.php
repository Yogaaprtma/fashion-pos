@extends('layouts.app')

@section('title', 'Laporan per Kasir')
@section('page-title', 'Laporan Performa Kasir')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Per Kasir</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box violet">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Performa Kasir</h1>
                <p class="subtitle">Ringkasan performa setiap kasir berdasarkan sesi, transaksi, dan total omzet.</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar-enhanced">
    <form method="GET" action="{{ route('reports.cashier') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;width:100%;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label class="form-label">Filter Kasir</label>
            <select name="user_id" class="form-control">
                <option value="">Semua Kasir</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Terapkan Filter
        </button>
    </form>
</div>

@if($kasirData->count() > 0)

{{-- Kasir Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
    @foreach($kasirData as $userId => $data)
    <div class="card" style="transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 28px rgba(79,70,229,0.10)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <img src="{{ $data['user']?->avatar_url }}" alt="{{ $data['user']?->name }}"
                     style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid #EEF2FF;">
                <div>
                    <div style="font-weight:800;color:#111827;">{{ $data['user']?->name }}</div>
                    <div style="font-size:12px;color:#6B7280;margin-top:2px;">
                        <span class="badge badge-primary">{{ $data['sessions']->count() }} sesi kerja</span>
                    </div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div style="background:#F8FAFC;border:1px solid #E5E7EB;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#6B7280;">Total Transaksi</div>
                    <div style="font-size:24px;font-weight:900;color:#4F46E5;margin-top:4px;">{{ number_format($data['total_trx']) }}</div>
                </div>
                <div style="background:#F8FAFC;border:1px solid #E5E7EB;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#6B7280;">Avg/Trx</div>
                    <div style="font-size:14px;font-weight:800;color:#059669;margin-top:4px;">Rp {{ number_format($data['avg_per_trx'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div style="padding:12px;background:linear-gradient(135deg,#EEF2FF,#EFF6FF);border-radius:10px;border:1px solid #C7D2FE;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#6B7280;">Total Omzet</div>
                <div style="font-size:18px;font-weight:900;color:#4F46E5;margin-top:4px;">Rp {{ number_format($data['total_sales'], 0, ',', '.') }}</div>
            </div>
            @if(abs($data['total_diff']) > 0)
            <div style="margin-top:8px;padding:10px 12px;background:{{ $data['total_diff'] >= 0 ? 'rgba(16,185,129,0.07)' : 'rgba(239,68,68,0.07)' }};border-radius:10px;border:1px solid {{ $data['total_diff'] >= 0 ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};">
                <div style="font-size:10px;font-weight:700;color:#6B7280;">Total Selisih Kasir</div>
                <div style="font-size:14px;font-weight:700;color:{{ $data['total_diff'] >= 0 ? '#059669' : '#DC2626' }};margin-top:2px;">
                    {{ $data['total_diff'] >= 0 ? '+' : '' }}Rp {{ number_format($data['total_diff'], 0, ',', '.') }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Detail Sesi --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Detail Setiap Sesi Kasir
        </div>
        <span class="badge badge-primary">{{ $sessions->count() }} sesi</span>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Kasir</th>
                    <th>Buka Sesi</th>
                    <th>Tutup Sesi</th>
                    <th style="text-align:center;">Total Trx</th>
                    <th style="text-align:right;">Total Penjualan</th>
                    <th style="text-align:right;">Selisih</th>
                    <th style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td style="font-weight:700;color:#111827;">{{ $session->user?->name }}</td>
                    <td style="font-size:13px;">{{ $session->opened_at->format('d M Y, H:i') }}</td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $session->closed_at?->format('d M Y, H:i') ?? '-' }}</td>
                    <td style="text-align:center;"><span class="badge badge-primary">{{ $session->total_transactions }}</span></td>
                    <td style="text-align:right;font-family:monospace;font-weight:700;">Rp {{ number_format($session->total_sales, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:700;color:{{ ($session->difference ?? 0) >= 0 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                        @if($session->difference !== null)
                            {{ $session->difference >= 0 ? '+' : '' }}Rp {{ number_format($session->difference, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($session->status === 'open')
                            <span class="badge badge-success">● Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tutup</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-state-icon">👤</div>
            <div class="empty-state-title">Belum ada data sesi kasir</div>
            <div class="empty-state-desc">Tidak ada sesi kasir pada periode {{ $dateFrom }} s/d {{ $dateTo }}.</div>
        </div>
    </div>
</div>
@endif

@endsection
