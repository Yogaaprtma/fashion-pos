@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- KPI Cards -->
<div class="grid-4 mb-6">

    <!-- Omzet -->
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="kpi-label">Omzet Hari Ini</div>
                <div class="kpi-value" style="font-size:22px;margin-top:6px">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-icon blue">💰</div>
        </div>
        @if($revenueGrowth != 0)
        <div>
            <span class="kpi-trend {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
                {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs(number_format($revenueGrowth, 1)) }}% vs kemarin
            </span>
        </div>
        @else
        <div class="kpi-sub">Mulai hari yang produktif!</div>
        @endif
    </div>

    <!-- Transaksi -->
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="kpi-label">Transaksi Hari Ini</div>
                <div class="kpi-value" style="font-size:34px;margin-top:6px">{{ $todayTransactions }}</div>
            </div>
            <div class="kpi-icon green">🧾</div>
        </div>
        <div class="kpi-sub">transaksi tercatat</div>
    </div>

    <!-- Laba Kotor -->
    <div class="kpi-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="kpi-label">Laba Kotor Hari Ini</div>
                <div class="kpi-value" style="font-size:22px;margin-top:6px">Rp {{ number_format($todayProfit, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-icon amber">📈</div>
        </div>
        <div>
            <span class="kpi-trend {{ $profitMargin >= 20 ? 'up' : 'flat' }}">
                Margin {{ number_format($profitMargin, 1) }}%
            </span>
        </div>
    </div>

    <!-- Stok Rendah -->
    <div class="kpi-card" style="{{ $lowStockCount > 0 ? 'border-color:var(--color-warning-border);' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="kpi-label">Stok Rendah</div>
                <div class="kpi-value" style="font-size:34px;margin-top:6px;color:{{ $lowStockCount > 0 ? 'var(--color-warning)' : 'var(--color-success)' }}">{{ $lowStockCount }}</div>
            </div>
            <div class="kpi-icon {{ $lowStockCount > 0 ? 'amber' : 'green' }}">📦</div>
        </div>
        @if($lowStockCount > 0)
        <a href="{{ route('inventory.stock.low') }}" class="kpi-trend down" style="display:inline-flex;text-decoration:none">
            Lihat Produk →
        </a>
        @else
        <div class="kpi-sub">Semua stok aman ✓</div>
        @endif
    </div>

</div>

<!-- Chart + Summary -->
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;margin-bottom:20px" class="dashboard-chart-row">

    <!-- Sales Chart -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:inline;vertical-align:-2px;margin-right:6px;color:var(--color-primary)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Grafik Penjualan
            </div>
            <div style="display:flex;gap:4px;">
                <button class="chip active" onclick="loadChart('week', this)" style="font-size:12px;padding:4px 12px">7 Hari</button>
                <button class="chip" onclick="loadChart('month', this)" style="font-size:12px;padding:4px 12px">30 Hari</button>
                <button class="chip" onclick="loadChart('year', this)" style="font-size:12px;padding:4px 12px">12 Bulan</button>
            </div>
        </div>
        <div class="card-body" style="height:250px;padding:16px">
            <canvas id="salesChart" style="width:100%;height:100%"></canvas>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div style="display:flex;flex-direction:column;gap:14px">
        <div class="card">
            <div class="card-body" style="padding:16px">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted);margin-bottom:14px">Ringkasan Bulan Ini</div>
                <div class="stat-row">
                    <span class="stat-row-label">Omzet</span>
                    <span class="stat-row-value">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Transaksi</span>
                    <span class="stat-row-value">{{ $monthTransactions }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Kasir Aktif</span>
                    <span class="stat-row-value" style="color:{{ $activeSessions > 0 ? 'var(--color-success)' : 'var(--text-muted)' }}">{{ $activeSessions }} sesi</span>
                </div>
                <div class="stat-row">
                    <span class="stat-row-label">Nilai Inventori</span>
                    <span class="stat-row-value" style="font-size:12px;color:var(--color-primary)">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Active Session -->
        @if(auth()->user()->activeSession())
        @php $mySession = auth()->user()->activeSession(); @endphp
        <div class="card" style="border-color:var(--color-success-border);background:var(--color-success-light)">
            <div class="card-body" style="padding:14px">
                <div class="flex-between mb-3">
                    <span style="font-size:12px;font-weight:700;color:#16A34A;display:flex;align-items:center;gap:6px">
                        <span style="width:7px;height:7px;background:#22C55E;border-radius:50%;display:inline-block"></span>
                        Sesi Kasir Aktif
                    </span>
                    <span style="font-size:11px;color:var(--text-muted)">{{ $mySession->opened_at->diffForHumans() }}</span>
                </div>
                <div style="font-size:12.5px;color:#166534;margin-bottom:3px">
                    Modal: <strong>Rp {{ number_format($mySession->opening_balance, 0, ',', '.') }}</strong>
                </div>
                <div style="font-size:12.5px;color:#166534;margin-bottom:12px">
                    Penjualan: <strong>Rp {{ number_format($mySession->total_sales, 0, ',', '.') }}</strong>
                </div>
                <a href="{{ route('pos.index') }}" class="btn btn-success btn-block btn-sm">
                    Lanjut Transaksi →
                </a>
            </div>
        </div>
        @else
        <div class="card" style="text-align:center">
            <div class="card-body" style="padding:20px 16px">
                <div style="font-size:28px;margin-bottom:8px">⏸️</div>
                <div style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:4px">Kasir Belum Dibuka</div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:14px">Mulai shift untuk mencatat transaksi</div>
                @if(auth()->user()->canAccess('pos.access'))
                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm btn-block">Buka Kasir</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Top Products + Recent Transactions -->
<div class="grid-2" style="gap:20px">

    <!-- Top Products -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">🏆 Produk Terlaris Bulan Ini</div>
            <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            @forelse($topProducts as $index => $product)
            <div style="display:flex;align-items:center;gap:14px;padding:13px 20px;border-bottom:1px solid var(--bg-elevated);transition:background .15s" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background=''">
                <div style="width:30px;height:30px;border-radius:8px;background:{{ ['#EFF6FF','#F0FDF4','#FFFBEB','#F0F9FF','#FFF1F2'][$index % 5] }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:{{ ['#2563EB','#16A34A','#D97706','#0284C7','#DC2626'][$index % 5] }};flex-shrink:0">
                    {{ $index + 1 }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $product->product_name }}</div>
                    <div style="font-size:11.5px;color:var(--text-muted)">{{ $product->total_qty }} terjual</div>
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--color-primary);font-family:var(--font-mono);flex-shrink:0">
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
            <div class="card-title">🧾 Transaksi Terbaru</div>
            <a href="{{ route('pos.history') }}" class="btn btn-sm btn-secondary">Semua</a>
        </div>
        <div class="card-body p-0">
            @forelse($recentTransactions as $trx)
            <a href="{{ route('pos.transaction.show', $trx) }}" style="display:flex;align-items:center;gap:14px;padding:13px 20px;border-bottom:1px solid var(--bg-elevated);text-decoration:none;transition:background .15s" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background=''">
                <div style="width:36px;height:36px;border-radius:var(--radius-md);background:var(--bg-elevated);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">🧾</div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)">{{ $trx->invoice_number }}</div>
                    <div style="font-size:11.5px;color:var(--text-muted)">
                        {{ $trx->cashierSession?->user?->name ?? '-' }} · {{ $trx->created_at->format('H:i') }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:13px;font-weight:700;color:var(--text-primary);font-family:var(--font-mono)">
                        Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                    </div>
                    <span class="badge badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
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

<style>
@media (max-width: 1100px) { .dashboard-chart-row { grid-template-columns: 1fr !important; } }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let chartData = @json($chartData);
    let salesChartInstance = null;

    function initChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChartInstance) salesChartInstance.destroy();

        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data.values,
                    borderColor: '#2563EB',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        borderColor: '#1E293B',
                        borderWidth: 1,
                        titleColor: '#F1F5F9',
                        bodyColor: '#94A3B8',
                        cornerRadius: 8,
                        padding: 10,
                        callbacks: {
                            label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw),
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(15,23,42,0.04)', drawBorder: false },
                        ticks: { color: '#94A3B8', font: { size: 11, family: 'Inter' } },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(15,23,42,0.04)', drawBorder: false },
                        ticks: {
                            color: '#94A3B8',
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
