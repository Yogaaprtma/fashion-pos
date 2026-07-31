@extends('layouts.app')

@section('title', 'Prediksi Restock & Reorder Assistant')
@section('page-title', 'Restock Assistant')

@section('content')

<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Inventori</span>
        <span class="sep">›</span>
        <span>Restock Assistant</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box" style="background:linear-gradient(135deg,#F59E0B,#D97706);">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h1>🤖 Restock Assistant</h1>
                <p class="subtitle">Sistem akan menganalisis kecepatan penjualan 30 hari terakhir dan memberi rekomendasi kapan harus memesan ulang stok.</p>
            </div>
        </div>
    </div>
</div>

{{-- Legend --}}
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
        <span style="width:12px;height:12px;border-radius:3px;background:#EF4444;display:inline-block;"></span> Kritis (Stok ≤ 7 hari)
    </div>
    <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
        <span style="width:12px;height:12px;border-radius:3px;background:#F59E0B;display:inline-block;"></span> Segera (8–14 hari)
    </div>
    <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
        <span style="width:12px;height:12px;border-radius:3px;background:#3B82F6;display:inline-block;"></span> Perlu Perhatian (15–30 hari)
    </div>
    <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
        <span style="width:12px;height:12px;border-radius:3px;background:#10B981;display:inline-block;"></span> Aman (> 30 hari)
    </div>
</div>

{{-- Stats Summary --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    <div class="card" style="border-top:4px solid #EF4444;">
        <div class="card-body" style="text-align:center;padding:16px;">
            <div style="font-size:28px;font-weight:800;color:#EF4444;">{{ $stats['critical'] }}</div>
            <div style="font-size:12px;color:var(--text-muted)">🔴 Kritis</div>
        </div>
    </div>
    <div class="card" style="border-top:4px solid #F59E0B;">
        <div class="card-body" style="text-align:center;padding:16px;">
            <div style="font-size:28px;font-weight:800;color:#F59E0B;">{{ $stats['warning'] }}</div>
            <div style="font-size:12px;color:var(--text-muted)">🟡 Segera</div>
        </div>
    </div>
    <div class="card" style="border-top:4px solid #3B82F6;">
        <div class="card-body" style="text-align:center;padding:16px;">
            <div style="font-size:28px;font-weight:800;color:#3B82F6;">{{ $stats['attention'] }}</div>
            <div style="font-size:12px;color:var(--text-muted)">🔵 Perhatian</div>
        </div>
    </div>
    <div class="card" style="border-top:4px solid #10B981;">
        <div class="card-body" style="text-align:center;padding:16px;">
            <div style="font-size:28px;font-weight:800;color:#10B981;">{{ $stats['safe'] }}</div>
            <div style="font-size:12px;color:var(--text-muted)">🟢 Aman</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="card-title">Rekomendasi Reorder per Produk Varian</h3>
        <div style="font-size:12px;color:var(--text-muted)">Data penjualan 30 hari terakhir · Sorted by urgensi</div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>Produk / Varian</th>
                        <th>Stok Saat Ini</th>
                        <th>Terjual /30 hari</th>
                        <th>Rata-rata /Hari</th>
                        <th>Estimasi Sisa Stok</th>
                        <th>Status</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recommendations as $rec)
                    @php
                        $statusColor = match($rec['status']) {
                            'critical'  => '#EF4444',
                            'warning'   => '#F59E0B',
                            'attention' => '#3B82F6',
                            default     => '#10B981',
                        };
                        $statusLabel = match($rec['status']) {
                            'critical'  => '🔴 Kritis',
                            'warning'   => '🟡 Segera',
                            'attention' => '🔵 Perhatian',
                            default     => '🟢 Aman',
                        };
                    @endphp
                    <tr style="border-left:3px solid {{ $statusColor }};">
                        <td>
                            <div style="font-weight:700;">{{ $rec['product_name'] }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $rec['variant_label'] }} · <code>{{ $rec['sku'] }}</code></div>
                        </td>
                        <td>
                            <strong style="color:{{ $statusColor }};">{{ $rec['stock'] }}</strong>
                        </td>
                        <td>{{ $rec['sold_30d'] }}</td>
                        <td>{{ number_format($rec['avg_per_day'], 1) }}</td>
                        <td>
                            @if($rec['days_remaining'] === null)
                                <span style="color:var(--text-muted)">∞ (tidak bergerak)</span>
                            @elseif($rec['days_remaining'] <= 0)
                                <span style="color:#EF4444;font-weight:700;">HABIS!</span>
                            @else
                                <strong>~{{ $rec['days_remaining'] }} hari</strong>
                            @endif
                        </td>
                        <td>
                            <span style="background:{{ $statusColor }}20;color:{{ $statusColor }};border:1px solid {{ $statusColor }}40;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:700;">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            @if($rec['recommendation'])
                            <div style="font-size:12px;max-width:200px;">{{ $rec['recommendation'] }}</div>
                            @else
                            <span style="color:var(--text-muted);font-size:12px;">Tidak perlu tindakan</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data produk varian aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
