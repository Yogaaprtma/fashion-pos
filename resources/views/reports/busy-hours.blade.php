@extends('layouts.app')
@section('title', 'Analisis Jam Sibuk')
@section('page-title', 'Analisis Jam Sibuk')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Jam Sibuk</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box rose">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1>Analisis Jam Sibuk Toko</h1>
                <p class="subtitle">Distribusi transaksi per jam untuk memahami pola kunjungan pelanggan dan mengoptimalkan operasional.</p>
            </div>
        </div>
        <form method="GET" style="display:flex;gap:8px;align-items:flex-end;flex-shrink:0;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Periode</label>
                <select name="days" class="form-control">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari Terakhir</option>
                </select>
            </div>
            <button class="btn btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Tampilkan
            </button>
        </form>
    </div>

    @php
        $peak = $heatmap->sortByDesc('trx_count')->first();
        $quiet = $heatmap->where('trx_count', 0)->count();
        $totalTrx = $heatmap->sum('trx_count');
    @endphp

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:#DC2626;">{{ $peak['label'] ?? '-' }}</div>
                <div class="ph-stat-lbl">Jam Paling Ramai ({{ $peak['trx_count'] ?? 0 }} trx)</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($totalTrx) }}</div>
                <div class="ph-stat-lbl">Total Transaksi {{ $days }} Hari</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="color:#D97706;">{{ $quiet }}</div>
                <div class="ph-stat-lbl">Jam Tanpa Transaksi</div>
            </div>
        </div>
    </div>
</div>

@php
    $maxTrx = $heatmap->max('trx_count') ?: 1;
@endphp

{{-- Heatmap Chart --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Distribusi Transaksi per Jam ({{ $days }} hari terakhir)
        </div>
        <div style="display:flex;gap:14px;font-size:12px;color:#6B7280;align-items:center;">
            <div style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#374151;border-radius:3px;display:inline-block;"></span>Sangat Sepi</div>
            <div style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#6366F1;border-radius:3px;display:inline-block;"></span>Normal</div>
            <div style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#F59E0B;border-radius:3px;display:inline-block;"></span>Ramai</div>
            <div style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#EF4444;border-radius:3px;display:inline-block;"></span>Sangat Ramai</div>
        </div>
    </div>
    <div class="card-body" style="padding:24px;">
        <div style="display:flex;flex-direction:column;gap:7px;">
            @foreach($heatmap as $row)
            @php
                $pct = $maxTrx > 0 ? ($row['trx_count'] / $maxTrx) * 100 : 0;
                $color = $pct > 75 ? '#EF4444' : ($pct > 40 ? '#F59E0B' : ($pct > 10 ? '#6366F1' : '#E5E7EB'));
                $textColor = $pct > 10 ? 'white' : '#9CA3AF';
            @endphp
            <div style="display:grid;grid-template-columns:68px 1fr 90px;align-items:center;gap:14px;">
                <div style="font-size:12px;font-family:monospace;color:#6B7280;text-align:right;font-weight:600;">{{ $row['label'] }}</div>
                <div style="height:30px;background:#F3F4F6;border-radius:8px;overflow:hidden;position:relative;">
                    <div style="width:{{ max(2, $pct) }}%;height:100%;background:{{ $color }};border-radius:8px;transition:width .6s cubic-bezier(0.34,1.56,0.64,1);display:flex;align-items:center;padding-left:10px;">
                        @if($row['trx_count'] > 0)
                        <span style="font-size:11px;font-weight:700;color:{{ $textColor }};white-space:nowrap;">{{ $row['trx_count'] }} trx</span>
                        @endif
                    </div>
                </div>
                <div style="font-size:11px;font-family:monospace;color:#6B7280;text-align:right;">{{ $row['trx_count'] > 0 ? 'Rp ' . number_format($row['total_sales'] / max(1,$row['trx_count']), 0, ',', '.') : '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
