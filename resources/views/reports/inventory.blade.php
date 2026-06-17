@extends('layouts.app')

@section('title', 'Laporan Inventori')
@section('page-title', 'Laporan Nilai Inventori')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Laporan</span>
        <span class="sep">›</span>
        <span>Inventori</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box amber">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1>Laporan Nilai Inventori</h1>
                <p class="subtitle">Ringkasan nilai stok berdasarkan HPP dan potensi nilai jual per kategori produk.</p>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <button class="btn btn-secondary" onclick="window.print()">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($totalSKU ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total SKU</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ number_format($totalQty ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Qty (pcs)</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:15px;">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Total Nilai HPP</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val" style="font-size:15px;color:#059669;">Rp {{ number_format($totalPotentialValue ?? 0, 0, ',', '.') }}</div>
                <div class="ph-stat-lbl">Potensi Nilai Jual</div>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Nilai Inventori per Kategori
        </div>
        <span class="badge badge-primary">{{ count($categoryStats ?? []) }} kategori</span>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kategori Utama</th>
                    <th style="text-align:center">Jumlah SKU</th>
                    <th style="text-align:center">Total Qty (Pcs)</th>
                    <th style="text-align:right">Total Nilai HPP</th>
                    <th style="text-align:right">Potensi Nilai Jual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryStats ?? [] as $stat)
                <tr>
                    <td style="font-weight:700;color:#111827;">{{ $stat->name }}</td>
                    <td style="text-align:center;"><span class="badge badge-secondary">{{ number_format($stat->sku_count, 0, ',', '.') }}</span></td>
                    <td style="text-align:center;"><span class="badge badge-primary">{{ number_format($stat->total_qty, 0, ',', '.') }}</span></td>
                    <td style="text-align:right;font-family:monospace;font-weight:600;">Rp {{ number_format($stat->total_buy_value, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;font-weight:700;color:#059669;">Rp {{ number_format($stat->total_sell_value, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-title">Data kategori kosong</div>
                            <div class="empty-state-desc">Belum ada data inventori yang tersedia.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($categoryStats ?? []) > 0)
            <tfoot>
                <tr style="font-weight:800;background:#F0F4FF;color:#4F46E5;font-size:13.5px;">
                    <td>TOTAL</td>
                    <td style="text-align:center;">{{ number_format($totalSKU ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:center;">{{ number_format($totalQty ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;">Rp {{ number_format($totalValue ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-family:monospace;color:#059669;">Rp {{ number_format($totalPotentialValue ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
