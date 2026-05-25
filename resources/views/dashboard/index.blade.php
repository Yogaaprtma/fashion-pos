@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
/* ── DASHBOARD / LIGHT FUTURE ───────────────────────────── */
.dashboard-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    margin-bottom: 22px;
    padding: 24px 26px;
    gap: 24px;
    flex-wrap: wrap;
    background:
        radial-gradient(circle at 83% 0%, rgba(59, 130, 246, .16), transparent 15rem),
        linear-gradient(120deg, #FFFFFF 0%, #FAFBFF 48%, #EFF6FF 100%);
    border: 1px solid #E5E7EB;
    border-radius: 20px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .025), 0 10px 28px rgba(37, 99, 235, .055);
}

.dashboard-hero::after {
    content: '';
    width: 170px;
    height: 170px;
    position: absolute;
    top: -108px;
    right: 17%;
    border: 1px solid rgba(59, 130, 246, .16);
    border-radius: 50%;
    box-shadow: 0 0 0 30px rgba(255,255,255,.26), 0 0 0 60px rgba(59,130,246,.045);
    pointer-events: none;
}

.dashboard-hero-title {
    font-family: 'Outfit', sans-serif;
    position: relative;
    z-index: 1;
    font-size: clamp(26px, 2.2vw, 34px);
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1.15;
    background: linear-gradient(105deg, #111827 0%, #111827 37%, #4F46E5 62%, #2563EB 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard-hero-sub {
    position: relative;
    z-index: 1;
    margin-top: 7px;
    color: #475569;
    font-size: 13px;
}

.hero-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
    gap: 10px;
}

/* KPI Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.kpi-card {
    --kpi-color: #4F46E5;
    --kpi-soft: #EEF2FF;
    --kpi-icon-bg: #E0E7FF;
    --kpi-border: #C7D2FE;
    min-height: 155px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(145deg, #FFFFFF 55%, var(--kpi-soft) 135%);
    border: 1px solid var(--kpi-border);
    border-radius: 17px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .025), 0 7px 20px rgba(15, 23, 42, .035);
    transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
}

.kpi-card::before {
    content: '';
    position: absolute;
    inset: 0 auto auto 0;
    width: 42px;
    height: 4px;
    background: var(--kpi-color);
    border-radius: 0 0 999px 0;
    opacity: 1;
    pointer-events: none;
}

.kpi-card::after {
    content: '';
    width: 110px;
    height: 110px;
    position: absolute;
    top: -52px;
    right: -44px;
    background: var(--kpi-color);
    border-radius: 50%;
    filter: blur(42px);
    opacity: .085;
    pointer-events: none;
}

.kpi-card:hover {
    border-color: var(--kpi-color);
    box-shadow: 0 16px 32px color-mix(in srgb, var(--kpi-color) 12%, transparent);
    transform: translateY(-3px);
}

.kpi-card.k-revenue { --kpi-color: #7C3AED; --kpi-soft: #FAF5FF; --kpi-icon-bg: #F3E8FF; --kpi-border: #E9D5FF; }
.kpi-card.k-trx { --kpi-color: #10B981; --kpi-soft: #ECFDF5; --kpi-icon-bg: #D1FAE5; --kpi-border: #BBF7D0; }
.kpi-card.k-profit { --kpi-color: #F97316; --kpi-soft: #FFF7ED; --kpi-icon-bg: #FFEDD5; --kpi-border: #FED7AA; }
.kpi-card.k-stock { --kpi-color: #2563EB; --kpi-soft: #EFF6FF; --kpi-icon-bg: #DBEAFE; --kpi-border: #BFDBFE; }

.kpi-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    z-index: 1;
    gap: 12px;
    margin-bottom: 9px;
}

.kpi-icon-wrap {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--kpi-color);
    background: var(--kpi-icon-bg) !important;
    border: 1px solid var(--kpi-border);
    border-radius: 13px;
    box-shadow: none !important;
}

.kpi-icon-wrap svg { stroke: currentColor; }

.kpi-label {
    margin-bottom: 7px;
    color: #475569;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .085em;
}

.kpi-value {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    letter-spacing: -.7px;
    line-height: 1;
    color: #111827;
}

.kpi-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    position: relative;
    z-index: 1;
    margin-top: 9px;
}

.kpi-trend.up { color: #047857; background: #D1FAE5; }
.kpi-trend.down { color: #B91C1C; background: #FEE2E2; }
.kpi-trend.flat { color: #C2410C; background: #FFEDD5; }
.kpi-sub { position: relative; z-index: 1; margin-top: 10px; color: #64748B; font-size: 11.5px; }

/* Chart Section */
.chart-section {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 310px;
    gap: 16px;
    margin-bottom: 20px;
}

.summary-stat { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); font-size: 12.5px; }
.summary-stat:last-child { border-bottom: none; }
.summary-stat .label { color: #64748B; }
.summary-stat .value { color: #111827; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; font-weight: 700; text-align: right; }

.session-live {
    border-color: #A7F3D0 !important;
    background: linear-gradient(145deg, #FFFFFF, #ECFDF5) !important;
}

.session-live-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .13);
}

/* Strong focal state for the unopened cashier widget. */
.chart-section > div:last-child > .card[style*="border-style:dashed"] {
    color: #FFFFFF;
    background: linear-gradient(135deg, #4F46E5 0%, #2563EB 55%, #0EA5E9 115%) !important;
    border: 1px solid rgba(255, 255, 255, .14) !important;
    box-shadow: 0 16px 34px rgba(37, 99, 235, .22);
}

.chart-section > div:last-child > .card[style*="border-style:dashed"] .card-body,
.chart-section > div:last-child > .card[style*="border-style:dashed"] .card-body > div {
    color: #FFFFFF !important;
}

.chart-section > div:last-child > .card[style*="border-style:dashed"] .card-body > div:nth-child(3) {
    color: #DBEAFE !important;
}

.chart-section > div:last-child > .card[style*="border-style:dashed"] .btn-primary {
    color: #1D4ED8;
    background: #FFFFFF;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .15);
}

.chart-section > div:last-child > .card[style*="border-style:dashed"] .btn-primary:hover {
    color: #3730A3;
    background: #EEF2FF;
}

.rank-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}

.rank-item:last-child { border-bottom: none; }
.rank-item:hover { background: #F8FAFC; }

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

.rank-1 { background: #FEF3C7; color: #B45309; }
.rank-2 { background: #F1F5F9; color: #475569; }
.rank-3 { background: #FFEDD5; color: #C2410C; }
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
.trx-item:hover { background: #F8FAFC; }

.trx-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    color: #4F46E5;
    background: #EEF2FF;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid #C7D2FE;
}

.trx-icon svg { stroke: currentColor; }

@media (max-width: 1200px) {
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
    .chart-section { grid-template-columns: 1fr; }
    .dashboard-hero-title { font-size: 27px; }
}

@media (max-width: 600px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; }
    .dashboard-hero { padding: 20px; }
    .dashboard-hero-title { font-size: 24px; }
    .dashboard-hero-sub { line-height: 1.65; }
    .kpi-card { min-height: 145px; padding: 17px; }
    .kpi-icon-wrap { width: 39px; height: 39px; }
}

@media (max-width: 410px) {
    .kpi-grid { grid-template-columns: 1fr; }
    .kpi-card { min-height: 134px; }
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
    <div class="kpi-card k-stock" style="{{ $lowStockCount > 0 ? 'border-color:var(--color-danger)' : '' }}">
        <div class="kpi-top">
            <div style="flex:1;min-width:0">
                <div class="kpi-label">Stok Rendah</div>
                <div class="kpi-value" style="font-size:38px;color:#111827">{{ $lowStockCount }}</div>
            </div>
            <div class="kpi-icon-wrap">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
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
                    <span class="value" style="color:{{ $activeSessions > 0 ? 'var(--color-success-text)' : 'var(--text-muted)' }}">
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
                    <span style="font-size:12px;font-weight:700;color:var(--color-success-text);display:flex;align-items:center;gap:7px">
                        <span class="session-live-dot"></span>
                        Sesi Kasir Aktif
                    </span>
                    <span style="font-size:11px;color:var(--text-muted)">{{ $mySession->opened_at->diffForHumans() }}</span>
                </div>
                <div style="font-size:12.5px;color:var(--color-success-text);margin-bottom:3px">
                    Modal: <strong>Rp {{ number_format($mySession->opening_balance, 0, ',', '.') }}</strong>
                </div>
                <div style="font-size:12.5px;color:var(--color-success-text);margin-bottom:14px">
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
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--color-warning)" stroke-width="2">
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
                    ['bg' => '#FBF1DF', 'text' => '#A66C1B', 'class' => 'rank-1'],
                    ['bg' => '#EFF2F6', 'text' => '#748097', 'class' => 'rank-2'],
                    ['bg' => '#F7EBE4', 'text' => '#A86842', 'class' => 'rank-3'],
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
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--color-accent)" stroke-width="2">
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
        grad.addColorStop(0, 'rgba(79, 70, 229, 0.20)');
        grad.addColorStop(0.55, 'rgba(59, 130, 246, 0.07)');
        grad.addColorStop(1, 'rgba(79, 70, 229, 0)');

        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data.values,
                    borderColor: '#2563EB',
                    backgroundColor: grad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#4F46E5',
                    pointHoverBorderColor: '#FFFFFF',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#FFFFFF',
                        borderColor: '#E5E7EB',
                        borderWidth: 1,
                        titleColor: '#111827',
                        bodyColor: '#475569',
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
                        grid: { color: 'rgba(148,163,184,0.18)', drawBorder: false },
                        ticks: { color: '#64748B', font: { size: 11, family: 'Inter' } },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(148,163,184,0.18)', drawBorder: false },
                        ticks: {
                            color: '#64748B',
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
