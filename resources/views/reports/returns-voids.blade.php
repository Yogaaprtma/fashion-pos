@extends('layouts.app')
@section('title', 'Laporan Retur & Void')
@section('content')
<div class="flex-between" style="margin-bottom:24px">
    <h1 class="page-title">↩️ Laporan Retur & Void Transaksi</h1>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control" style="height:36px;font-size:13px">
        <span>—</span>
        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control" style="height:36px;font-size:13px">
        <button class="btn btn-primary btn-sm">Filter</button>
    </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
    <div class="card" style="text-align:center;padding:20px">
        <div style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:8px">Total Retur</div>
        <div style="font-size:32px;font-weight:900;color:#FB7185">{{ $returns->count() }}</div>
        <div style="font-family:monospace;color:var(--text-secondary)">Rp {{ number_format($returns->sum('total_refund'), 0, ',', '.') }}</div>
    </div>
    <div class="card" style="text-align:center;padding:20px">
        <div style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;margin-bottom:8px">Total Void</div>
        <div style="font-size:32px;font-weight:900;color:#F59E0B">{{ $voids->count() }}</div>
        <div style="font-family:monospace;color:var(--text-secondary)">Rp {{ number_format($voids->sum('grand_total'), 0, ',', '.') }}</div>
    </div>
</div>

<h2 style="font-size:16px;font-weight:700;margin:20px 0 12px">Daftar Retur Barang</h2>
<div class="card p-0">
    <table class="table">
        <thead>
            <tr><th>No. Retur</th><th>No. Transaksi Asal</th><th>Alasan</th><th>Status</th><th>Total Refund</th><th>Diajukan oleh</th></tr>
        </thead>
        <tbody>
            @forelse($returns as $r)
            <tr>
                <td style="font-family:monospace;font-weight:700">{{ $r->return_number }}</td>
                <td>{{ $r->transaction?->invoice_number }}</td>
                <td>{{ Str::limit($r->reason, 40) }}</td>
                <td>
                    @if($r->status === 'approved') <span class="badge" style="background:#D1FAE5;color:#065F46">Approved</span>
                    @elseif($r->status === 'rejected') <span class="badge" style="background:#FEE2E2;color:#991B1B">Ditolak</span>
                    @else <span class="badge" style="background:#FEF3C7;color:#92400E">Pending</span> @endif
                </td>
                <td style="font-family:monospace;color:#FB7185">Rp {{ number_format($r->total_refund, 0, ',', '.') }}</td>
                <td>{{ $r->requestedBy?->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center" style="padding:30px;color:var(--text-muted)">Tidak ada retur dalam periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 style="font-size:16px;font-weight:700;margin:24px 0 12px">Daftar Transaksi Void (Dibatalkan)</h2>
<div class="card p-0">
    <table class="table">
        <thead>
            <tr><th>No. Transaksi</th><th>Total</th><th>Alasan Void</th><th>Di-void oleh</th><th>Waktu Void</th></tr>
        </thead>
        <tbody>
            @forelse($voids as $v)
            <tr>
                <td style="font-family:monospace;font-weight:700">{{ $v->invoice_number }}</td>
                <td style="font-family:monospace;color:#F59E0B">Rp {{ number_format($v->grand_total, 0, ',', '.') }}</td>
                <td>{{ Str::limit($v->void_reason, 50) }}</td>
                <td>{{ $v->cashierSession?->user?->name ?? '-' }}</td>
                <td>{{ $v->voided_at?->format('d/m/Y H:i') ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center" style="padding:30px;color:var(--text-muted)">Tidak ada void dalam periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
