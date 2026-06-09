@extends('layouts.app')

@section('title', 'Laporan Inventori')
@section('page-title', 'Laporan Nilai Inventori')

@section('content')

<div class="grid grid-3 mb-4">
    <div class="stat-card primary">
        <div class="stat-label">Total Item Berbeda (SKU)</div>
        <div class="stat-value">{{ number_format($totalSKU ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Total Qty Fisik (Pcs)</div>
        <div class="stat-value">{{ number_format($totalQty ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">Total Nilai (HPP)</div>
        <div class="stat-value">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header flex-between">
        <div class="card-title">Nilai Inventori Berdasarkan Kategori</div>
        <button class="btn btn-secondary" onclick="window.print()">🖨️ Cetak</button>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kategori Utama</th>
                    <th style="text-align:center">Jumlah SKU</th>
                    <th style="text-align:center">Total Qty (Pcs)</th>
                    <th style="text-align:right">Total Nilai Pembelian (HPP)</th>
                    <th style="text-align:right">Potensi Nilai Jual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryStats ?? [] as $stat)
                <tr>
                    <td style="font-weight:600">{{ $stat->name }}</td>
                    <td style="text-align:center">{{ number_format($stat->sku_count, 0, ',', '.') }}</td>
                    <td style="text-align:center">{{ number_format($stat->total_qty, 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($stat->total_buy_value, 0, ',', '.') }}</td>
                    <td style="text-align:right; color:var(--color-success)">Rp {{ number_format($stat->total_sell_value, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:30px">Data kategori kosong.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="font-weight:700; background:var(--bg-elevated)">
                    <td>TOTAL</td>
                    <td style="text-align:center">{{ number_format($totalSKU ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:center">{{ number_format($totalQty ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right; color:var(--color-success)">Rp {{ number_format($totalPotentialValue ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
