<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'FashionPOS') }}</title>
    <meta name="description" content="Sistem POS & Manajemen Swalayan Pakaian">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Topbar gradient line */
        .topbar::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,58,237,0.5) 30%, rgba(6,182,212,0.4) 70%, transparent);
        }
        /* Sidebar gradient accent top */
        .sidebar-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-layout">

    <!-- ================================================================
         SIDEBAR
    ================================================================ -->
    <aside class="sidebar" id="sidebar">

        <!-- Logo Header -->
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-logo" title="FashionPOS">
                <div class="sidebar-logo-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                </div>
                <div style="min-width:0; overflow:hidden;">
                    <div class="sidebar-logo-text">FashionPOS</div>
                    <div class="sidebar-logo-sub">{{ \App\Models\StoreSetting::get('store_name', 'Swalayan Pakaian') }}</div>
                </div>
            </a>
        </div>

        <!-- Scrollable Navigation -->
        <nav class="sidebar-nav" id="sidebarNav">

            <!-- UTAMA -->
            <div class="nav-group-label">Utama</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-label">Dashboard</span>
            </a>

            @if(auth()->user()->canAccess('pos.access'))
            <a href="{{ route('pos.index') }}"
               class="nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}"
               data-tooltip="Kasir (POS)">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0011.586 3H4a2 2 0 00-2 2v13a2 2 0 002 2z"/>
                </svg>
                <span class="nav-label">Kasir (POS)</span>
                @if(auth()->user()->activeSession())
                    <span class="nav-badge" style="background:#22C55E; color:white; font-size:9px; padding:1px 5px;">AKTIF</span>
                @endif
            </a>
            @endif

            <!-- INVENTORI -->
            @if(auth()->user()->canAccess('inventory.view'))
            <div class="nav-group-label">Inventori</div>

            <a href="{{ route('inventory.products.index') }}"
               class="nav-item {{ request()->routeIs('inventory.products.*') || request()->routeIs('inventory.barcode*') ? 'active' : '' }}"
               data-tooltip="Produk">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="nav-label">Produk</span>
                @php $lowStockCount = \App\Models\Product::with('variants')->get()->filter(fn($p) => $p->isLowStock())->count(); @endphp
                @if($lowStockCount > 0)
                    <span class="nav-badge">{{ $lowStockCount }}</span>
                @endif
            </a>

            <a href="{{ route('inventory.categories.index') }}"
               class="nav-item {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}"
               data-tooltip="Kategori">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="nav-label">Kategori</span>
            </a>

            <a href="{{ route('inventory.stock.index') }}"
               class="nav-item {{ request()->routeIs('inventory.stock.*') ? 'active' : '' }}"
               data-tooltip="Manajemen Stok">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="nav-label">Manajemen Stok</span>
            </a>
            @endif

            <!-- PENGADAAN -->
            @if(auth()->user()->canAccess('purchase.view'))
            <div class="nav-group-label">Pengadaan</div>

            <a href="{{ route('purchase.suppliers.index') }}"
               class="nav-item {{ request()->routeIs('purchase.suppliers.*') ? 'active' : '' }}"
               data-tooltip="Supplier">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-label">Supplier</span>
            </a>

            <a href="{{ route('purchase.orders.index') }}"
               class="nav-item {{ request()->routeIs('purchase.orders.*') ? 'active' : '' }}"
               data-tooltip="Purchase Order">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="nav-label">Purchase Order</span>
            </a>
            @endif

            <!-- LAPORAN -->
            @if(auth()->user()->canAccess('report.sales'))
            <div class="nav-group-label">Laporan</div>

            <a href="{{ route('reports.sales') }}"
               class="nav-item {{ request()->routeIs('reports.sales*') ? 'active' : '' }}"
               data-tooltip="Laporan Penjualan">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="nav-label">Lap. Penjualan</span>
            </a>

            @if(auth()->user()->canAccess('report.financial'))
            <a href="{{ route('reports.financial') }}"
               class="nav-item {{ request()->routeIs('reports.financial*') ? 'active' : '' }}"
               data-tooltip="Laporan Keuangan">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="nav-label">Lap. Keuangan</span>
            </a>
            @endif

            <a href="{{ route('reports.inventory') }}"
               class="nav-item {{ request()->routeIs('reports.inventory*') ? 'active' : '' }}"
               data-tooltip="Laporan Inventori">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="nav-label">Lap. Inventori</span>
            </a>

            <a href="{{ route('reports.cashier') }}"
               class="nav-item {{ request()->routeIs('reports.cashier') ? 'active' : '' }}"
               data-tooltip="Lap. per Kasir">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-label">Lap. per Kasir</span>
            </a>

            <a href="{{ route('reports.discount') }}"
               class="nav-item {{ request()->routeIs('reports.discount') ? 'active' : '' }}"
               data-tooltip="Laporan Diskon">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="nav-label">Lap. Diskon</span>
            </a>

            <a href="{{ route('reports.category') }}"
               class="nav-item {{ request()->routeIs('reports.category') ? 'active' : '' }}"
               data-tooltip="Lap. per Kategori">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="nav-label">Lap. Kategori</span>
            </a>

            <a href="{{ route('reports.payment-method') }}"
               class="nav-item {{ request()->routeIs('reports.payment-method') ? 'active' : '' }}"
               data-tooltip="Lap. per Metode Bayar">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="nav-label">Lap. Metode Bayar</span>
            </a>

            <a href="{{ route('reports.returns-voids') }}"
               class="nav-item {{ request()->routeIs('reports.returns-voids') ? 'active' : '' }}"
               data-tooltip="Laporan Retur & Void">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                </svg>
                <span class="nav-label">Lap. Retur & Void</span>
            </a>

            <a href="{{ route('reports.busy-hours') }}"
               class="nav-item {{ request()->routeIs('reports.busy-hours') ? 'active' : '' }}"
               data-tooltip="Analisis Jam Sibuk">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="nav-label">Jam Sibuk</span>
            </a>

            <a href="{{ route('pos.history') }}"
               class="nav-item {{ request()->routeIs('pos.history') ? 'active' : '' }}"
               data-tooltip="Riwayat Transaksi">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="nav-label">Riwayat Transaksi</span>
            </a>

            @if(auth()->user()->canAccess('pos.return'))
            <a href="{{ route('returns.index') }}"
               class="nav-item {{ request()->routeIs('returns.*') ? 'active' : '' }}"
               data-tooltip="Manajemen Retur">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                </svg>
                <span class="nav-label">Manajemen Retur</span>
            </a>
            @endif
            @endif

            <!-- SISTEM -->
            @if(auth()->user()->hasAnyRole(['admin', 'manajemen']))
            <div class="nav-group-label">Sistem</div>

            @if(auth()->user()->canAccess('asset.view'))
            <a href="{{ route('assets.index') }}"
               class="nav-item {{ request()->routeIs('assets.*') ? 'active' : '' }}"
               data-tooltip="Aset">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="nav-label">Aset</span>
            </a>
            @endif

            @if(auth()->user()->canAccess('user.manage'))
            <a href="{{ route('users.index') }}"
               class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
               data-tooltip="Pengguna">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="nav-label">Pengguna</span>
            </a>
            @endif

            <a href="{{ route('audit-logs.index') }}"
               class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"
               data-tooltip="Audit Log">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="nav-label">Audit Log</span>
            </a>

            @if(auth()->user()->canAccess('setting.manage'))
            <a href="{{ route('settings.index') }}"
               class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}"
               data-tooltip="Pengaturan">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-label">Pengaturan</span>
            </a>
            @endif
            @endif

        </nav>

        <!-- Sidebar Footer: User Profile (always visible) -->
        <div class="sidebar-footer">
            <div class="user-card">
                <a href="{{ route('profile.show') }}" style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;text-decoration:none;">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar" style="flex-shrink:0;">
                    <div style="min-width:0;">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role?->display_name ?? 'User' }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0">
                    @csrf
                    <button type="submit" class="btn-ghost btn btn-sm btn-icon" title="Logout" style="width:32px;height:32px;padding:0">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- ================================================================
         MAIN CONTENT
    ================================================================ -->
    <div class="main-content" id="mainContent">

        <!-- TOPBAR -->
        <header class="topbar" style="position:sticky;">
            <div class="topbar-left">
                <!-- Hamburger / Collapse Toggle -->
                <button onclick="toggleSidebar()" class="btn-sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar">
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
            </div>

            <div class="topbar-actions">
                <!-- Date/Time -->
                <div class="topbar-datetime" id="currentDateTime"></div>

                <!-- Low Stock Alert -->
                @if(($lowStockCount ?? 0) > 0)
                <a href="{{ route('inventory.stock.low') }}" class="notif-btn" title="{{ $lowStockCount }} produk stok rendah" style="color:#D97706; border-color:#FDE68A; background:#FFFBEB;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="notif-dot" style="background:#D97706"></div>
                </a>
                @endif

                <!-- Open Cashier -->
                @if(auth()->user()->canAccess('pos.access') && !request()->routeIs('pos.*'))
                <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0011.586 3H4a2 2 0 00-2 2v13a2 2 0 002 2z"/>
                    </svg>
                    Buka Kasir
                </a>
                @endif

                <!-- User Avatar Dropdown (simple) -->
                <div style="position:relative;" id="userDropdownWrapper">
                    <button onclick="toggleUserDropdown()" style="display:flex;align-items:center;gap:8px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:var(--radius-full);padding:4px 12px 4px 4px;cursor:pointer;font-size:13px;font-weight:600;color:var(--text-primary);">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:26px;height:26px;border-radius:50%;object-fit:cover;">
                        <span style="display:none" class="topbar-username">{{ auth()->user()->name }}</span>
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="userDropdown" style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);min-width:180px;overflow:hidden;z-index:500;">
                        <div style="padding:12px 14px;border-bottom:1px solid var(--border);">
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ auth()->user()->name }}</div>
                            <div style="font-size:11.5px;color:var(--text-muted);">{{ auth()->user()->role?->display_name ?? '' }}</div>
                        </div>
                        <a href="{{ route('settings.index') }}" style="display:flex;align-items:center;gap:9px;padding:10px 14px;font-size:13px;color:var(--text-secondary);text-decoration:none;transition:background 0.15s;" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil & Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="display:flex;align-items:center;gap:9px;padding:10px 14px;font-size:13px;color:#DC2626;background:none;border:none;cursor:pointer;width:100%;text-align:left;transition:background 0.15s;" onmouseover="this.style.background='var(--color-danger-light)'" onmouseout="this.style.background='transparent'">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE BODY -->
        <main class="page-body animate-fade-in">

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success mb-4" id="flash-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="alert alert-error mb-4" id="flash-error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Mobile Bottom Nav -->
<nav class="mobile-nav">
    <div class="mobile-nav-inner">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Beranda
        </a>
        @if(auth()->user()->canAccess('pos.access'))
        <a href="{{ route('pos.index') }}" class="mobile-nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 0011.586 3H4a2 2 0 00-2 2v13a2 2 0 002 2z"/></svg>
            Kasir
        </a>
        @endif
        @if(auth()->user()->canAccess('inventory.view'))
        <a href="{{ route('inventory.products.index') }}" class="mobile-nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Stok
        </a>
        @endif
        @if(auth()->user()->canAccess('report.sales'))
        <a href="{{ route('reports.sales') }}" class="mobile-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Laporan
        </a>
        @endif
        <a href="{{ route('settings.index') }}" class="mobile-nav-item {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Lainnya
        </a>
    </div>
</nav>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- App JS -->
<script>
    // ================================================================
    // DATE/TIME
    // ================================================================
    function updateDateTime() {
        const el = document.getElementById('currentDateTime');
        if (el) {
            const now = new Date();
            el.textContent = now.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short', year:'numeric'})
                + ' ' + now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
        }
    }
    updateDateTime();
    setInterval(updateDateTime, 10000);

    // ================================================================
    // SIDEBAR: COLLAPSE (Desktop) / SLIDE (Mobile)
    // ================================================================
    const SIDEBAR_KEY = 'fashionpos_sidebar_collapsed';
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    // Restore collapse state from localStorage
    if (window.innerWidth > 1024 && localStorage.getItem(SIDEBAR_KEY) === 'true') {
        sidebar.classList.add('collapsed');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 1024) {
            // Mobile: slide in/out
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        } else {
            // Desktop: collapse/expand
            sidebar.classList.toggle('collapsed');
            localStorage.setItem(SIDEBAR_KEY, sidebar.classList.contains('collapsed'));
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            // Re-apply collapsed state
            if (localStorage.getItem(SIDEBAR_KEY) === 'true') {
                sidebar.classList.add('collapsed');
            }
        }
    });

    // Keyboard shortcut: [ to collapse
    document.addEventListener('keydown', e => {
        if (e.key === '[' && !e.ctrlKey && !e.metaKey && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) {
            if (window.innerWidth > 1024) toggleSidebar();
        }
        if (e.key === 'Escape') {
            closeSidebar();
            closeUserDropdown();
        }
    });

    // ================================================================
    // USER DROPDOWN
    // ================================================================
    function toggleUserDropdown() {
        const dd = document.getElementById('userDropdown');
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }

    function closeUserDropdown() {
        const dd = document.getElementById('userDropdown');
        if (dd) dd.style.display = 'none';
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('userDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) closeUserDropdown();
    });

    // ================================================================
    // TOAST NOTIFICATIONS
    // ================================================================
    function showToast(message, type = 'success', duration = 3500) {
        const container = document.getElementById('toastContainer');
        const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
            <span class="toast-message">${message}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--text-muted);padding:0;line-height:1;margin-left:auto;">×</button>
        `;
        container.appendChild(toast);
        setTimeout(() => { if (toast.parentNode) toast.remove(); }, duration);
    }

    // ================================================================
    // AUTO-DISMISS FLASH MESSAGES
    // ================================================================
    setTimeout(() => {
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity 0.4s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            }
        });
    }, 4000);

    // ================================================================
    // FORMAT CURRENCY (global helper)
    // ================================================================
    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount || 0));
    }

    function formatNumber(n) {
        return new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
    }
</script>

@stack('scripts')
</body>
</html>
