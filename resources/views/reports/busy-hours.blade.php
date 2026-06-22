@extends('layouts.app')
@section('title', 'Analisis Jam Sibuk')
@section('content')
<div class="flex-between" style="margin-bottom:24px">
    <h1 class="page-title">⏰ Analisis Jam Sibuk Toko</h1>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <select name="days" class="form-control" style="height:36px;font-size:13px">
            <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
            <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
            <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari Terakhir</option>
        </select>
        <button class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div>

@php
    $maxTrx = $heatmap->max('trx_count') ?: 1;
@endphp

<div class="card" style="padding:28px">
    <div style="font-size:14px;font-weight:700;margin-bottom:20px;color:var(--text-secondary)">Distribusi Transaksi per Jam ({{ $days }} hari terakhir)</div>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($heatmap as $row)
        @php
            $pct = $maxTrx > 0 ? ($row['trx_count'] / $maxTrx) * 100 : 0;
            $color = $pct > 75 ? '#EF4444' : ($pct > 40 ? '#F59E0B' : ($pct > 10 ? '#6366F1' : '#374151'));
        @endphp
        <div style="display:grid;grid-template-columns:60px 1fr 80px;align-items:center;gap:12px">
            <div style="font-size:12px;font-family:monospace;color:var(--text-muted);text-align:right">{{ $row['label'] }}</div>
            <div style="height:28px;background:var(--bg-elevated);border-radius:6px;overflow:hidden">
                <div style="width:{{ max(2, $pct) }}%;height:100%;background:{{ $color }};border-radius:6px;transition:width .5s ease;display:flex;align-items:center;padding-left:8px">
                    @if($row['trx_count'] > 0)
                    <span style="font-size:11px;font-weight:700;color:white;white-space:nowrap">{{ $row['trx_count'] }} trx</span>
                    @endif
                </div>
            </div>
            <div style="font-size:11px;font-family:monospace;color:var(--text-muted);text-align:right">{{ $row['trx_count'] > 0 ? 'Rp ' . number_format($row['total_sales'] / max(1,$row['trx_count']), 0, ',', '.') : '-' }}</div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:20px;display:flex;gap:20px;font-size:12px;color:var(--text-muted)">
        <div><span style="display:inline-block;width:12px;height:12px;background:#374151;border-radius:3px;margin-right:5px"></span>Sangat Sepi</div>
        <div><span style="display:inline-block;width:12px;height:12px;background:#6366F1;border-radius:3px;margin-right:5px"></span>Normal</div>
        <div><span style="display:inline-block;width:12px;height:12px;background:#F59E0B;border-radius:3px;margin-right:5px"></span>Ramai</div>
        <div><span style="display:inline-block;width:12px;height:12px;background:#EF4444;border-radius:3px;margin-right:5px"></span>Sangat Ramai</div>
    </div>
</div>

<div class="card" style="margin-top:16px">
    @php
        $peak = $heatmap->sortByDesc('trx_count')->first();
        $quiet = $heatmap->where('trx_count', 0)->count();
    @endphp
    <div style="font-weight:700;margin-bottom:12px">📊 Ringkasan Analisis</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;text-align:center">
        <div>
            <div style="font-size:28px;font-weight:900;color:#EF4444">{{ $peak['label'] }}</div>
            <div style="font-size:12px;color:var(--text-muted)">Jam Paling Ramai ({{ $peak['trx_count'] }} trx)</div>
        </div>
        <div>
            <div style="font-size:28px;font-weight:900;color:var(--color-primary-light)">{{ number_format($heatmap->sum('trx_count')) }}</div>
            <div style="font-size:12px;color:var(--text-muted)">Total Transaksi {{ $days }} hari</div>
        </div>
        <div>
            <div style="font-size:28px;font-weight:900;color:#F59E0B">{{ $quiet }}</div>
            <div style="font-size:12px;color:var(--text-muted)">Jam Tanpa Transaksi</div>
        </div>
    </div>
</div>
@endsection
