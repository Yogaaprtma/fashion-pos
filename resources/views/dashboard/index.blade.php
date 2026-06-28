@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
/* ── DASHBOARD SPECIFIC ─────────────────────────────────── */
.dashboard-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 20px;
    flex-wrap: wrap;
}

.dashboard-hero-title {
    font-family: 'Outfit', sans-serif;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.8px;
    line-height: 1.2;
    background: linear-gradient(135deg, #F0F0FF 30%, #A78BFA 65%, #67E8F9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard-hero-sub {
    font-size: 13.5px;
    color: var(--text-muted);
    margin-top: 4px;
}

.hero-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

/* KPI Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

/* KPI Card enhanced */
.kpi-card {
    border-radius: 18px;
    padding: 22px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: default;
    background: var(--bg-card);
    border: 1px solid var(--border);
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -30px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    filter: blur(50px);
    opacity: 0.15;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.6); }
.kpi-card:hover::before { opacity: 0.28; }

.kpi-card.k-revenue::before { background: #7C3AED; }
.kpi-card.k-trx::before { background: #10B981; }
.kpi-card.k-profit::before { background: #06B6D4; }
.kpi-card.k-stock::before { background: #F59E0B; }

.kpi-card.k-revenue { border-color: rgba(124,58,237,0.2); }
.kpi-card.k-trx { border-color: rgba(16,185,129,0.2); }
.kpi-card.k-profit { border-color: rgba(6,182,212,0.2); }
.kpi-card.k-stock { border-color: rgba(245,158,11,0.2); }

.kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
.kpi-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.kpi-card.k-revenue .kpi-icon-wrap { background: rgba(124,58,237,0.15); box-shadow: 0 4px 16px rgba(124,58,237,0.25); }
.kpi-card.k-trx .kpi-icon-wrap { background: rgba(16,185,129,0.12); box-shadow: 0 4px 16px rgba(16,185,129,0.2); }
.kpi-card.k-profit .kpi-icon-wrap { background: rgba(6,182,212,0.12); box-shadow: 0 4px 16px rgba(6,182,212,0.2); }
.kpi-card.k-stock .kpi-icon-wrap { background: rgba(245,158,11,0.12); box-shadow: 0 4px 16px rgba(245,158,11,0.2); }

.kpi-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-muted);
    margin-bottom: 6px;
}

.kpi-value {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
    color: var(--text-primary);
}

.kpi-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    margin-top: 10px;
}

.kpi-trend.up { background: rgba(16,185,129,0.12); color: #34D399; }
.kpi-trend.down { background: rgba(244,63,94,0.12); color: #FB7185; }
.kpi-trend.flat { background: rgba(245,158,11,0.1); color: #FCD34D; }
.kpi-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 10px; }

/* Chart Section */
.chart-section {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 18px;
    margin-bottom: 20px;
}

/* Summary card inner rows */
.summary-stat { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.summary-stat:last-child { border-bottom: none; }
.summary-stat .label { color: var(--text-muted); }
.summary-stat .value { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--text-primary); font-size: 12.5px; }

/* Session alert */
.session-live {
    border-color: rgba(16,185,129,0.3) !important;
    background: rgba(16,185,129,0.04) !important;
}

.session-live-dot {
    width: 8px;
    height: 8px;
    background: #22C55E;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
    box-shadow: 0 0 8px #22C55E;
}

/* Ranking items */
.rank-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}

.rank-item:last-child { border-bottom: none; }
.rank-item:hover { background: rgba(124,58,237,0.04); }

.rank-num {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

.rank-1 { background: rgba(245,158,11,0.2); color: #FCD34D; }
.rank-2 { background: rgba(148,163,184,0.15); color: #94A3B8; }
.rank-3 { background: rgba(180,83,9,0.15); color: #D97706; }
.rank-n { background: var(--bg-elevated); color: var(--text-muted); }

.trx-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    text-decoration: none;
    transition: background 0.15s;
}

.trx-item:last-child { border-bottom: none; }
.trx-item:hover { background: rgba(124,58,237,0.04); }

.trx-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: var(--bg-elevated);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid var(--border);
}

@media (max-width: 1200px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
    .chart-section { grid-template-columns: 1fr; }
    .dashboard-hero-title { font-size: 22px; }
}

@media (max-width: 600px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@section('content')

<!-- Dashboard Hero Header -->
<div class="dashboard-hero">
    <div>
        <div class="dashboard-hero-title">Selamat datang, {{ Str::words(auth()->user()->name, 1, '') }}! 👋</div>
        <div class="dashboard-hero-sub">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · Pantau performa toko Anda secara real-time</div>
    </div>
    <div class="hero-actions">
        @if(auth()->user()->canAccess('pos.access') && !request()->routeIs('pos.*'))
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0011.586 3H4a2 2 0 00-2 2v13a2 2 0 002 2z"/>
            </svg>
            Buka Kasir
        </a>
        @endif
        @if(auth()->user()->canAccess('report.sales'))
        <a href="{{ route('reports.sales') }}" class="btn btn-secondary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Laporan
        </a>
        @endif
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">

    <!-- Omzet -->
    <div class="kpi-card k-revenue">
        <div class="kpi-top">
            <div style="flex:1;min-width:0">
                <div class="kpi-label">Omzet Hari Ini</div>
                <div class="kpi-value" style="font-size:22px">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-icon-wrap">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#A78BFA" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        @if($revenueGrowth != 0)
        <span class="kpi-trend {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
            {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs(number_format($revenueGrowth, 1)) }}% vs kemarin
        </span>
        @else
        <div class="kpi-sub">Mulai hari yang produktif! ✨</div>
        @endif
    </div>

    <!-- Transaksi -->
    <div class="kpi-card k-trx">
        <div class="kpi-top">
            <div style="flex:1;min-width:0">
                <div class="kpi-label">Transaksi Hari Ini</div>
                <div class="kpi-value" style="font-size:38px">{{ $todayTransactions }}</div>
            </div>
            <div class="kpi-icon-wrap">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#34D399" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
        <div class="kpi-sub">transaksi tercatat hari ini</div>
    </div>

    <!-- Laba -->
    <div class="kpi-card k-profit">
        <div class="kpi-top">
            <div style="flex:1;min-width:0">
                <div class="kpi-label">Laba Kotor Hari Ini</div>
                <div class="kpi-value" style="font-size:22px">Rp {{ number_format($todayProfit, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-icon-wrap">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#67E8F9" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <span class="kpi-trend {{ $profitMargin >= 20 ? 'up' : 'flat' }}">
            Margin {{ number_format($profitMargin, 1) }}%
        </span>
    </div>

    <!-- Stok Rendah -->
    <div class="kpi-card k-stock" style="{{ $lowStockCount > 0 ? 'border-color:rgba(245,158,11,0.35)' : '' }}">
        <div class="kpi-top">
            <div style="flex:1;min-width:0">
                <div class="kpi-label">Stok Rendah</div>
                <div class="kpi-value" style="font-size:38px;color:{{ $lowStockCount > 0 ? '#FCD34D' : '#34D399' }}">{{ $lowStockCount }}</div>
            </div>
            <div class="kpi-icon-wrap">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="{{ $lowStockCount > 0 ? '#FCD34D' : '#34D399' }}" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        @if($lowStockCount > 0)
        <a href="{{ route('inventory.stock.low') }}" class="kpi-trend down" style="text-decoration:none;display:inline-flex">
            ⚠ Lihat Produk →
        </a>
        @else
        <div class="kpi-sub">✓ Semua stok aman</div>
        @endif
    </div>

</div>

<!-- Chart + Summary -->
<div class="chart-section">

    <!-- Sales Chart Card -->
    <div class="card card-glow">
        <div class="card-header" style="padding: 18px 22px;">
            <div class="card-title" style="font-size:15px">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:var(--color-primary-light)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Grafik Penjualan
            </div>
            <div style="display:flex;gap:6px">
                <button class="chip active" onclick="loadChart('week', this)" style="font-size:11.5px;padding:4px 12px">7 Hari</button>
                <button class="chip" onclick="loadChart('month', this)" style="font-size:11.5px;padding:4px 12px">30 Hari</button>
                <button class="chip" onclick="loadChart('year', this)" style="font-size:11.5px;padding:4px 12px">12 Bln</button>
            </div>
        </div>
        <div class="card-body" style="height:260px;padding:20px">
            <canvas id="salesChart" style="width:100%;height:100%"></canvas>
        </div>
    </div>

    <!-- Right Column: Monthly Summary + Session -->
    <div style="display:flex;flex-direction:column;gap:14px">

        <!-- Monthly Summary -->
        <div class="card" style="border-color:rgba(124,58,237,0.15)">
            <div class="card-header" style="padding:14px 18px">
                <div class="card-title" style="font-size:13px">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#A78BFA" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ringkasan Bulan Ini
                </div>
            </div>
            <div class="card-body" style="padding:16px 18px">
                <div class="summary-stat">
                    <span class="label">Total Omzet</span>
                    <span class="value" style="color:var(--color-primary-light)">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Transaksi</span>
                    <span class="value">{{ $monthTransactions }} txn</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Kasir Aktif</span>
                    <span class="value" style="color:{{ $activeSessions > 0 ? '#34D399' : 'var(--text-muted)' }}">
                        {{ $activeSessions > 0 ? '● ' : '' }}{{ $activeSessions }} sesi
                    </span>
                </div>
                <div class="summary-stat">
                    <span class="label">Nilai Inventori</span>
                    <span class="value" style="font-size:11.5px">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Active Session Card -->
        @if(auth()->user()->activeSession())
        @php $mySession = auth()->user()->activeSession(); @endphp
        <div class="card session-live">
            <div class="card-body" style="padding:16px 18px">
                <div class="flex-between mb-3">
                    <span style="font-size:12px;font-weight:700;color:#22C55E;display:flex;align-items:center;gap:7px">
                        <span class="session-live-dot"></span>
                        Sesi Kasir Aktif
                    </span>
                    <span style="font-size:11px;color:var(--text-muted)">{{ $mySession->opened_at->diffForHumans() }}</span>
                </div>
                <div style="font-size:12.5px;color:#34D399;margin-bottom:3px">
                    Modal: <strong>Rp {{ number_format($mySession->opening_balance, 0, ',', '.') }}</strong>
                </div>
                <div style="font-size:12.5px;color:#34D399;margin-bottom:14px">
                    Penjualan: <strong>Rp {{ number_format($mySession->total_sales, 0, ',', '.') }}</strong>
                </div>
                <a href="{{ route('pos.index') }}" class="btn btn-success btn-block btn-sm">
                    Lanjut Transaksi →
                </a>
            </div>
        </div>
        @elseif(auth()->user()->canAccess('pos.access'))
        <div class="card" style="border-style:dashed;text-align:center">
            <div class="card-body" style="padding:24px 18px">
                <div style="font-size:32px;margin-bottom:10px;opacity:0.35">⏸</div>
                <div style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:4px">Kasir Belum Dibuka</div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:14px">Mulai shift untuk mencatat transaksi</div>
                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm btn-block">Buka Kasir</a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Top Products + Recent Transactions -->
<div class="grid-2" style="gap:18px">

    <!-- Top Products -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#FCD34D" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Produk Terlaris Bulan Ini
            </div>
            <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-secondary">Semua →</a>
        </div>
        <div class="card-body p-0">
            @forelse($topProducts as $index => $product)
            @php
                $rankColors = [
                    ['bg' => 'rgba(245,158,11,0.18)', 'text' => '#FCD34D', 'class' => 'rank-1'],
                    ['bg' => 'rgba(148,163,184,0.15)', 'text' => '#94A3B8', 'class' => 'rank-2'],
                    ['bg' => 'rgba(180,83,9,0.15)', 'text' => '#D97706', 'class' => 'rank-3'],
                ];
                $rc = $rankColors[$index] ?? ['bg' => 'var(--bg-elevated)', 'text' => 'var(--text-muted)', 'class' => 'rank-n'];
            @endphp
            <div class="rank-item">
                <div class="rank-num {{ $rc['class'] }}">{{ $index + 1 }}</div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $product->product_name }}</div>
                    <div style="font-size:11.5px;color:var(--text-muted)">{{ $product->total_qty }} unit terjual</div>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--color-primary-light);font-family:'JetBrains Mono',monospace;flex-shrink:0">
                    Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:50px 24px">
                <div class="empty-state-icon">📊</div>
                <div class="empty-state-title">Belum ada data penjualan</div>
                <div class="empty-state-desc">Mulai catat transaksi untuk melihat produk terlaris di sini.</div>
                @if(auth()->user()->canAccess('pos.access'))
                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">Buka Kasir Sekarang</a>
                @endif
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#67E8F9" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Transaksi Terbaru
            </div>
            <a href="{{ route('pos.history') }}" class="btn btn-sm btn-secondary">Semua →</a>
        </div>
        <div class="card-body p-0">
            @forelse($recentTransactions as $trx)
            <a href="{{ route('pos.transaction.show', $trx) }}" class="trx-item">
                <div class="trx-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--color-primary-light)" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)">{{ $trx->invoice_number }}</div>
                    <div style="font-size:11.5px;color:var(--text-muted)">
                        {{ $trx->cashierSession?->user?->name ?? '-' }} · {{ $trx->created_at->format('H:i') }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:13px;font-weight:700;color:var(--text-primary);font-family:'JetBrains Mono',monospace">
                        Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                    </div>
                    <span class="badge badge-{{ $trx->status_color }}" style="font-size:10px">{{ $trx->status_label }}</span>
                </div>
            </a>
            @empty
            <div class="empty-state" style="padding:50px 24px">
                <div class="empty-state-icon">🧾</div>
                <div class="empty-state-title">Belum ada transaksi</div>
                <div class="empty-state-desc">Transaksi yang dicatat akan muncul di sini secara real-time.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let chartData = @json($chartData);
    let salesChartInstance = null;

    function initChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChartInstance) salesChartInstance.destroy();

        /* Dual gradient fill */
        const grad = ctx.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(124, 58, 237, 0.25)');
        grad.addColorStop(0.5, 'rgba(6, 182, 212, 0.08)');
        grad.addColorStop(1, 'rgba(124, 58, 237, 0.0)');

        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data.values,
                    borderColor: '#A78BFA',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#7C3AED',
                    pointBorderColor: '#0A0A18',
                    pointBorderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#67E8F9',
                    pointHoverBorderColor: '#0A0A18',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#121228',
                        borderColor: 'rgba(124,58,237,0.4)',
                        borderWidth: 1,
                        titleColor: '#F0F0FF',
                        bodyColor: '#9CA3C4',
                        cornerRadius: 12,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: ctx => '  Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw),
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                        ticks: { color: '#5B5B8A', font: { size: 11, family: 'Inter' } },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                        ticks: {
                            color: '#5B5B8A',
                            font: { size: 11, family: 'Inter' },
                            callback: v => {
                                if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
                                if (v >= 1000) return 'Rp ' + (v/1000).toFixed(0) + 'rb';
                                return 'Rp ' + v;
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    function loadChart(period, btn) {
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        fetch(`/dashboard/chart/${period}`)
            .then(r => r.json())
            .then(d => initChart(d));
    }

    initChart(chartData);
</script>
@endpush
