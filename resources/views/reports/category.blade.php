@extends('layouts.app')
@section('title', 'Laporan per Kategori')
@section('content')
<div class="flex-between" style="margin-bottom:24px">
    <h1 class="page-title">🏷️ Laporan Penjualan per Kategori</h1>
    <div style="display:flex;gap:10px">
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="height:36px;font-size:13px">
            <span>—</span>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="height:36px;font-size:13px">
            <button class="btn btn-primary btn-sm">Filter</button>
        </form>
    </div>
</div>
<div class="card p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-right">Total Qty Terjual</th>
                <th class="text-right">Total Penjualan</th>
                <th class="text-right">Estimasi Profit</th>
                <th class="text-right">Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $cat => $d)
            <tr>
                <td style="font-weight:600">{{ $cat }}</td>
                <td class="text-right">{{ number_format($d['total_qty']) }} pcs</td>
                <td class="text-right" style="font-family:monospace">Rp {{ number_format($d['total_sales'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-family:monospace;color:#34D399">Rp {{ number_format($d['total_profit'], 0, ',', '.') }}</td>
                <td class="text-right">{{ $d['total_sales'] > 0 ? number_format(($d['total_profit'] / $d['total_sales']) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center" style="padding:40px;color:var(--text-muted)">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        @if($data->count())
        <tfoot>
            <tr style="font-weight:900;background:var(--bg-elevated)">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('total_qty')) }} pcs</td>
                <td class="text-right" style="font-family:monospace">Rp {{ number_format($data->sum('total_sales'), 0, ',', '.') }}</td>
                <td class="text-right" style="font-family:monospace;color:#34D399">Rp {{ number_format($data->sum('total_profit'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
