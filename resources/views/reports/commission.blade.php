@extends('layouts.app')

@section('title', 'Laporan Komisi SPG')
@section('page-title', 'Laporan Komisi SPG / Sales')

@section('content')

{{-- Page Header --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Komisi SPG</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box indigo">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Komisi SPG / Sales</h1>
                <p class="subtitle">Rekapitalisasi omset dan bonus komisi SPG/Pramuniaga per periode (Rate: {{ $commissionRate }}%).</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter Box --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.commission') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div>
                <label class="form-label">Pilih SPG / Pramuniaga</label>
                <select name="salesperson_id" class="form-select">
                    <option value="">Semua SPG</option>
                    @foreach($salespersons as $spg)
                        <option value="{{ $spg->id }}" {{ $salespersonId == $spg->id ? 'selected' : '' }}>
                            {{ $spg->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    🔍 Filter Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards per SPG --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:16px;margin-bottom:24px;">
    @forelse($summary as $sum)
    <div class="card" style="border-top:4px solid var(--color-primary);">
        <div class="card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div style="font-weight:700;font-size:16px;color:var(--text-primary)">👩‍💼 {{ $sum->salesperson->name ?? 'Pramuniaga' }}</div>
                <span class="badge badge-primary">{{ $sum->total_transactions }} Transaksi</span>
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Total Omset Penjualan</div>
            <div style="font-size:18px;font-weight:800;color:var(--text-primary);margin-bottom:12px">
                Rp {{ number_format($sum->total_sales, 0, ',', '.') }}
            </div>
            <div style="padding:10px 14px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:10px;display:flex;justify-content:space-between;align-items:center">
                <span style="font-size:12px;font-weight:600;color:var(--color-success-text)">Bonus Komisi ({{ $commissionRate }}%)</span>
                <span style="font-size:16px;font-weight:800;color:var(--color-success-text)">Rp {{ number_format($sum->total_commission, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1;">
        <div class="card-body" style="text-align:center;padding:40px;color:var(--text-muted)">
            Belum ada transaksi ber-komisi pada periode ini.
        </div>
    </div>
    @endforelse
</div>

{{-- Transactions Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rincian Transaksi Komisi</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>SPG / Pramuniaga</th>
                        <th>Kasir Shift</th>
                        <th>Total Transaksi</th>
                        <th>Komisi ({{ $commissionRate }}%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td><strong>{{ $trx->invoice_number }}</strong></td>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-indigo">👩‍💼 {{ $trx->salesperson->name ?? '-' }}</span>
                        </td>
                        <td>{{ $trx->cashierSession->user->name ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</strong></td>
                        <td><strong style="color:var(--color-success-text)">+ Rp {{ number_format($trx->commission_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Data transaksi tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer">
        {{ $transactions->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
