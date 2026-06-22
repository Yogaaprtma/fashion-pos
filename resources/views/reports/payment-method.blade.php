@extends('layouts.app')
@section('title', 'Laporan per Metode Pembayaran')
@section('content')
<div class="flex-between" style="margin-bottom:24px">
    <h1 class="page-title">💳 Laporan per Metode Pembayaran</h1>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="height:36px;font-size:13px">
        <span>—</span>
        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="height:36px;font-size:13px">
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:24px">
    @foreach($data as $method => $d)
    <div class="card" style="text-align:center;padding:20px">
        <div style="font-size:24px;margin-bottom:6px">💳</div>
        <div style="font-weight:700;font-size:15px;margin-bottom:4px">{{ $method }}</div>
        <div style="font-size:22px;font-weight:900;font-family:monospace;color:var(--color-primary-light)">Rp {{ number_format($d['total_amount'], 0, ',', '.') }}</div>
        <div style="color:var(--text-muted);font-size:12px;margin-top:4px">{{ $d['total_count'] }} transaksi</div>
    </div>
    @endforeach
</div>
<div class="card p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th class="text-right">Jumlah Transaksi</th>
                <th class="text-right">Total Nominal</th>
                <th class="text-right">Rata-rata per Trx</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $method => $d)
            <tr>
                <td style="font-weight:600">{{ $method }}</td>
                <td class="text-right">{{ number_format($d['total_count']) }}</td>
                <td class="text-right" style="font-family:monospace">Rp {{ number_format($d['total_amount'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-family:monospace">Rp {{ $d['total_count'] > 0 ? number_format($d['total_amount'] / $d['total_count'], 0, ',', '.') : 0 }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="padding:40px;color:var(--text-muted)">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
