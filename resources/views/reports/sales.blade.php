@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')

<!-- Filter Laporan -->
<div class="card mb-4">
    <div class="card-body" style="padding:16px">
        <form action="{{ route('reports.sales') }}" method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="form-group" style="margin:0;flex:1;min-width:150px">
                <label class="form-label" style="font-size:11px">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate) }}">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:150px">
                <label class="form-label" style="font-size:11px">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}">
            </div>
            <div class="form-group" style="margin:0;flex:1;min-width:150px">
                <label class="form-label" style="font-size:11px">Status</label>
                <select name="status" class="form-control">
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('reports.sales.export-pdf', request()->all()) }}" target="_blank" class="btn btn-secondary" style="color:var(--color-danger)">
                    📄 Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan -->
<div class="grid grid-3 mb-6">
    <div class="stat-card primary">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">{{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:10px;color:var(--text-muted);font-family:monospace;margin-top:2px">Rp</div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ number_format($summary['transaction_count'] ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px">transaksi</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">Barang Terjual</div>
        <div class="stat-value">{{ number_format($summary['items_sold'] ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px">pcs</div>
    </div>
</div>

<div class="grid grid-2" style="gap:20px">
    <!-- Penjualan Harian -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Penjualan per Hari</div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Transaksi</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailySales ?? [] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                        <td>{{ $day->count }}</td>
                        <td class="currency">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Produk Terlaris</div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk & Varian</th>
                        <th>Terjual</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts ?? [] as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $item->product_name }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">{{ $item->variant_label }}</div>
                        </td>
                        <td>{{ $item->total_qty }}</td>
                        <td class="currency">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
