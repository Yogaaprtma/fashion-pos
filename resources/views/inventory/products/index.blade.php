@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Daftar Produk')

@push('styles')
<style>
    .products-page {
        --product-primary: #4F46E5;
        --product-blue: #3B82F6;
        --product-success: #10B981;
        --product-warning: #F59E0B;
        --product-danger: #EF4444;
    }

    .products-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 18px;
        padding: 24px 26px;
        background:
            radial-gradient(circle at 88% 0%, rgba(59, 130, 246, .14), transparent 17rem),
            linear-gradient(120deg, #FFFFFF 0%, #FAFBFF 52%, #EFF6FF 100%);
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .025), 0 10px 28px rgba(37, 99, 235, .045);
    }

    .products-hero::after {
        content: '';
        width: 180px;
        height: 180px;
        position: absolute;
        top: -124px;
        right: 17%;
        border: 1px solid rgba(79, 70, 229, .12);
        border-radius: 50%;
        box-shadow: 0 0 0 34px rgba(255,255,255,.28), 0 0 0 68px rgba(59,130,246,.035);
        pointer-events: none;
    }

    .products-hero__content,
    .products-hero__actions { position: relative; z-index: 1; }

    .products-breadcrumb {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 9px;
        color: #64748B;
        font-size: 11px;
        font-weight: 700;
    }

    .products-breadcrumb a { color: #4F46E5; text-decoration: none; }
    .products-breadcrumb svg { width: 12px; height: 12px; }

    .products-title {
        color: #111827;
        font-family: var(--font-sans);
        font-size: clamp(25px, 2vw, 32px);
        font-weight: 800;
        letter-spacing: -.045em;
        line-height: 1.15;
    }

    .products-subtitle {
        margin-top: 7px;
        color: #64748B;
        font-size: 13px;
        line-height: 1.65;
    }

    .products-quick-stats {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 12px;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
    }

    .products-quick-stats span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .products-quick-stats i {
        width: 4px;
        height: 4px;
        background: #CBD5E1;
        border-radius: 50%;
    }

    .products-hero__actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
    }

    .products-hero__actions .btn { min-height: 42px; border-radius: 12px; }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .summary-card {
        --summary-color: #4F46E5;
        --summary-soft: #EEF2FF;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 13px;
        position: relative;
        overflow: hidden;
        padding: 16px;
        color: inherit;
        background: linear-gradient(145deg, #FFFFFF 50%, var(--summary-soft) 145%);
        border: 1px solid #E5E7EB;
        border-radius: 17px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 7px 20px rgba(15,23,42,.035);
        text-decoration: none;
        transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease;
    }

    .summary-card::before {
        content: '';
        width: 36px;
        height: 3px;
        position: absolute;
        top: 0;
        left: 0;
        background: var(--summary-color);
        border-radius: 0 999px 999px 0;
    }

    .summary-card:hover {
        color: inherit;
        border-color: color-mix(in srgb, var(--summary-color) 36%, #E5E7EB);
        box-shadow: 0 13px 28px color-mix(in srgb, var(--summary-color) 10%, transparent);
        transform: translateY(-3px);
    }

    .summary-card--categories { --summary-color: #3B82F6; --summary-soft: #EFF6FF; }
    .summary-card--stock { --summary-color: #F59E0B; --summary-soft: #FFFBEB; }
    .summary-card--visible { --summary-color: #10B981; --summary-soft: #ECFDF5; }

    .summary-card__icon {
        width: 43px;
        height: 43px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        color: var(--summary-color);
        background: var(--summary-soft);
        border: 1px solid color-mix(in srgb, var(--summary-color) 22%, transparent);
        border-radius: 13px;
        transition: transform .24s ease;
    }

    .summary-card:hover .summary-card__icon { transform: rotate(-4deg) scale(1.05); }
    .summary-card__icon svg { width: 20px; height: 20px; }

    .summary-card__value {
        display: block;
        color: #111827;
        font-family: var(--font-sans);
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -.04em;
        line-height: 1;
    }

    .summary-card__label { display: block; margin-top: 5px; color: #64748B; font-size: 10px; font-weight: 800; letter-spacing: .055em; text-transform: uppercase; }

    .product-filter {
        margin-bottom: 16px;
        padding: 16px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 7px 20px rgba(15,23,42,.035);
    }

    .product-filter__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 11px;
    }

    .product-filter__title { color: #334155; font-size: 11px; font-weight: 800; letter-spacing: .075em; text-transform: uppercase; }
    .product-filter__result { color: #64748B; font-size: 11px; font-weight: 600; }

    .product-filter__form { display: grid; grid-template-columns: minmax(240px, 1fr) 210px 165px auto; gap: 9px; align-items: center; }

    .product-filter .form-control {
        height: 44px;
        background-color: #F8FAFC;
        border-radius: 12px;
    }

    .product-filter .search-input-icon { left: 14px; color: #4F46E5; }
    .product-filter .search-input .form-control { padding-left: 43px; }
    .product-filter__actions { display: flex; align-items: center; gap: 7px; }
    .product-filter__actions .btn { height: 44px; border-radius: 12px; }

    .active-filters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 11px;
        padding-top: 11px;
        border-top: 1px solid #F1F5F9;
    }

    .active-filters__label { color: #64748B; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .active-filter {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        color: #4338CA;
        background: #EEF2FF;
        border: 1px solid #C7D2FE;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .product-data-card {
        overflow: hidden;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 19px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 10px 26px rgba(15,23,42,.045);
    }

    .product-grid-scroll { overflow: auto; scrollbar-width: thin; scrollbar-color: #CBD5E1 transparent; }
    .product-table { min-width: 1080px; border-collapse: separate; border-spacing: 0; }

    .product-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        height: 48px;
        padding: 0 14px;
        color: #475569;
        background: #F8FAFC;
        border-bottom: 1px solid #E5E7EB;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .075em;
    }

    .product-table tbody tr { border: 0; transition: background .22s ease, box-shadow .22s ease; }
    .product-table tbody tr:nth-child(even) { background: #FCFDFF; }
    .product-table tbody tr:hover { background: #F7F9FF; box-shadow: inset 3px 0 0 #4F46E5; }

    .product-table tbody td {
        padding: 14px;
        color: #475569;
        border-bottom: 1px solid #EEF2F7;
        font-size: 12.5px;
        vertical-align: middle;
    }

    .product-table tbody tr:last-child td { border-bottom: 0; }
    .product-row-number { color: #94A3B8; font-family: var(--font-mono); font-size: 11px; font-weight: 700; }

    .product-photo {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        overflow: hidden;
        color: #6366F1;
        background: linear-gradient(145deg, #EEF2FF, #EFF6FF);
        border: 1px solid #E0E7FF;
        border-radius: 14px;
        box-shadow: 0 5px 14px rgba(15,23,42,.07);
    }

    .product-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s ease; }
    .product-table tbody tr:hover .product-photo img { transform: scale(1.06); }
    .product-photo__fallback { display: grid; place-items: center; width: 100%; height: 100%; }
    .product-photo__fallback svg { width: 26px; height: 26px; }

    .product-name {
        display: inline-block;
        max-width: 290px;
        overflow: hidden;
        color: #111827;
        font-size: 13.5px;
        font-weight: 800;
        line-height: 1.35;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-name:hover { color: #4F46E5; }
    .product-sku { margin-top: 4px; color: #64748B; font-family: var(--font-mono); font-size: 10.5px; }
    .product-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; margin-top: 6px; }

    .product-meta__item {
        display: inline-flex;
        align-items: center;
        max-width: 145px;
        overflow: hidden;
        padding: 3px 7px;
        color: #475569;
        background: #F8FAFC;
        border: 1px solid #E5E7EB;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .category-pill { color: #4338CA; background: #EEF2FF; border-color: #C7D2FE; }

    .product-price { color: #111827; font-family: var(--font-mono); font-size: 13px; font-weight: 800; white-space: nowrap; }
    .product-cost { margin-top: 5px; color: #64748B; font-size: 9.5px; white-space: nowrap; }
    .product-margin { display: inline-block; margin-top: 5px; padding: 2px 6px; color: #047857; background: #ECFDF5; border-radius: 999px; font-size: 9px; font-weight: 800; }

    .stock-cell { min-width: 145px; }
    .stock-line { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .stock-value { color: #111827; font-family: var(--font-mono); font-size: 13px; font-weight: 800; }

    .stock-state {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .stock-state::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .stock-state--safe { color: #047857; }
    .stock-state--safe::before { background: #10B981; }
    .stock-state--low { color: #B45309; }
    .stock-state--low::before { background: #F59E0B; }
    .stock-state--out { color: #B91C1C; }
    .stock-state--out::before { background: #EF4444; }

    .stock-progress { height: 5px; overflow: hidden; margin-top: 8px; background: #E5E7EB; border-radius: 999px; }
    .stock-progress__bar { height: 100%; background: #10B981; border-radius: inherit; }
    .stock-progress__bar--low { background: #F59E0B; }
    .stock-progress__bar--out { background: #EF4444; }
    .stock-variants { margin-top: 5px; color: #64748B; font-size: 9.5px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border: 1px solid;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 800;
    }

    .status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .status-pill--active { color: #047857; background: #ECFDF5; border-color: #A7F3D0; }
    .status-pill--active::before { background: #10B981; box-shadow: 0 0 0 3px rgba(16,185,129,.11); }
    .status-pill--inactive { color: #64748B; background: #F8FAFC; border-color: #E2E8F0; }
    .status-pill--inactive::before { background: #94A3B8; }

    .product-actions { display: flex; align-items: center; gap: 6px; }
    .product-action {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        color: #475569;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        box-shadow: 0 2px 7px rgba(15,23,42,.035);
        transition: transform .2s ease, color .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .product-action svg { width: 16px; height: 16px; transition: transform .2s ease; }
    .product-action:hover { color: #4F46E5; background: #EEF2FF; border-color: #A5B4FC; box-shadow: 0 8px 16px rgba(79,70,229,.12); transform: translateY(-2px); }
    .product-action:hover svg { transform: rotate(-4deg) scale(1.05); }
    .product-action--edit:hover { color: #1D4ED8; background: #EFF6FF; border-color: #93C5FD; }

    .products-empty { padding: 60px 24px !important; text-align: center; }
    .products-empty__icon { width: 70px; height: 70px; display: grid; place-items: center; margin: 0 auto 16px; color: #4F46E5; background: linear-gradient(145deg, #EEF2FF, #EFF6FF); border: 1px solid #C7D2FE; border-radius: 21px; }
    .products-empty__title { color: #111827; font-size: 15px; font-weight: 800; }
    .products-empty__text { max-width: 360px; margin: 7px auto 18px; color: #64748B; font-size: 12px; line-height: 1.7; }
    .products-empty__actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; }

    .product-data-card__footer { padding: 13px 16px; background: #FFFFFF; border-top: 1px solid #E5E7EB; }

    @media (max-width: 1180px) {
        .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .product-filter__form { grid-template-columns: minmax(220px, 1fr) 190px 150px; }
        .product-filter__actions { grid-column: 1 / -1; justify-content: flex-end; }
    }

    @media (max-width: 760px) {
        .products-hero { align-items: flex-start; padding: 21px; }
        .products-hero, .products-hero__actions { flex-direction: column; }
        .products-hero__actions { width: 100%; align-items: stretch; }
        .products-hero__actions .btn { width: 100%; }
        .product-filter__form { grid-template-columns: 1fr; }
        .product-filter__actions { grid-column: auto; justify-content: stretch; }
        .product-filter__actions .btn { flex: 1; }
    }

    @media (max-width: 480px) {
        .summary-grid { grid-template-columns: 1fr; }
        .products-hero { border-radius: 17px; }
        .product-filter, .product-data-card { border-radius: 16px; }
    }
</style>
@endpush

@section('content')
@php
    $visibleActiveProducts = $products->getCollection()->where('is_active', true)->count();
@endphp

<div class="products-page">
    <header class="products-hero">
        <div class="products-hero__content">
            <nav class="products-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Inventori</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Produk</span>
            </nav>
            <h1 class="products-title">Katalog Produk</h1>
            <p class="products-subtitle">Kelola produk, harga, varian, dan ketersediaan stok dari satu ruang kerja.</p>
            <div class="products-quick-stats">
                <span>{{ number_format($products->total(), 0, ',', '.') }} produk ditemukan</span>
                <i></i>
                <span>{{ $categories->count() }} kategori aktif</span>
                <i></i>
                <span>{{ $lowStockCount }} stok rendah</span>
            </div>
        </div>
        <div class="products-hero__actions">
            <a href="{{ route('inventory.barcode-generator') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                Cetak Barcode
            </a>
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                Tambah Produk
            </a>
        </div>
    </header>

    <section class="summary-grid" aria-label="Ringkasan produk">
        <div class="summary-card">
            <span class="summary-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 7 8-4 8 4-8 4-8-4Z" stroke-linejoin="round"/><path d="m4 7 8 4 8-4v10l-8 4-8-4V7Z" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="summary-card__value">{{ number_format($products->total(), 0, ',', '.') }}</span><span class="summary-card__label">Total Produk</span></span>
        </div>
        <a href="{{ route('inventory.categories.index') }}" class="summary-card summary-card--categories">
            <span class="summary-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h.01M4 3h7l9 9-8 8-9-9V4a1 1 0 0 1 1-1Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="summary-card__value">{{ $categories->count() }}</span><span class="summary-card__label">Kategori Aktif</span></span>
        </a>
        <a href="{{ route('inventory.stock.low') }}" class="summary-card summary-card--stock">
            <span class="summary-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4m0 4h.01M10.3 3.9 2.5 17.5A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="summary-card__value">{{ $lowStockCount }}</span><span class="summary-card__label">Stok Rendah</span></span>
        </a>
        <div class="summary-card summary-card--visible">
            <span class="summary-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h10" stroke-linecap="round"/><path d="m17 17 2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="summary-card__value">{{ $visibleActiveProducts }}/{{ $products->count() }}</span><span class="summary-card__label">Aktif di Halaman</span></span>
        </div>
    </section>

    <section class="product-filter" aria-label="Pencarian dan filter produk">
        <div class="product-filter__header">
            <div class="product-filter__title">Cari & Filter</div>
            <div class="product-filter__result">{{ number_format($products->total(), 0, ',', '.') }} hasil</div>
        </div>
        <form action="{{ route('inventory.products.index') }}" method="GET" class="product-filter__form">
            <div class="search-input">
                <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                <input type="search" name="search" class="form-control" placeholder="Cari nama, SKU, atau brand..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <select name="category_id" class="form-control" aria-label="Filter kategori">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_status" class="form-control" aria-label="Filter status stok">
                <option value="">Semua Stok</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Stok Habis</option>
            </select>
            <div class="product-filter__actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.4-4.4M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" stroke-linecap="round"/></svg>
                    Terapkan
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                    <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary" title="Reset semua filter">Reset</a>
                @endif
            </div>
        </form>
        @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
            <div class="active-filters">
                <span class="active-filters__label">Filter aktif:</span>
                @if(request('search')) <span class="active-filter">Pencarian: {{ request('search') }}</span> @endif
                @if(request('category_id')) <span class="active-filter">Kategori: {{ $categories->firstWhere('id', request('category_id'))?->name ?? '-' }}</span> @endif
                @if(request('stock_status')) <span class="active-filter">Stok: {{ request('stock_status') === 'out' ? 'Habis' : 'Rendah' }}</span> @endif
            </div>
        @endif
    </section>

    <section class="product-data-card" aria-label="Data produk">
        <div class="product-grid-scroll">
            <table class="product-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="90">Foto</th>
                        <th>Informasi Produk</th>
                        <th width="150">Kategori</th>
                        <th width="155">Harga</th>
                        <th width="175">Stok</th>
                        <th width="105">Status</th>
                        <th width="105">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        @php
                            $totalStock = $product->total_stock;
                            $isOutOfStock = $totalStock <= 0;
                            $isLowStock = !$isOutOfStock && $product->isLowStock();
                            $stockProgress = min(100, ($totalStock / max(((int) $product->min_stock) * 3, 1)) * 100);
                            $marginPercent = (float) $product->sell_price > 0
                                ? (((float) $product->sell_price - (float) $product->buy_price) / (float) $product->sell_price) * 100
                                : 0;
                        @endphp
                        <tr>
                            <td><span class="product-row-number">{{ $products->firstItem() + $index }}</span></td>
                            <td>
                                <div class="product-photo">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                                    <span class="product-photo__fallback" hidden>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="m4 7 8-4 8 4-8 4-8-4Z" stroke-linejoin="round"/><path d="m4 7 8 4 8-4v10l-8 4-8-4V7Z" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('inventory.products.show', $product) }}" class="product-name">{{ $product->name }}</a>
                                <div class="product-sku">SKU · {{ $product->sku }}</div>
                                <div class="product-meta">
                                    @if($product->brand)<span class="product-meta__item">{{ $product->brand }}</span>@endif
                                    @if($product->barcode)<span class="product-meta__item">Barcode · {{ $product->barcode }}</span>@endif
                                    <span class="product-meta__item">{{ $product->variants->count() }} varian</span>
                                </div>
                            </td>
                            <td><span class="product-meta__item category-pill">{{ $product->category?->name ?? 'Tanpa kategori' }}</span></td>
                            <td>
                                <div class="product-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
                                <div class="product-cost">Modal Rp {{ number_format($product->buy_price, 0, ',', '.') }}</div>
                                <span class="product-margin">Margin {{ number_format($marginPercent, 1) }}%</span>
                            </td>
                            <td class="stock-cell">
                                <div class="stock-line">
                                    <span class="stock-value">{{ number_format($totalStock, 0, ',', '.') }}</span>
                                    @if($isOutOfStock)
                                        <span class="stock-state stock-state--out">Habis</span>
                                    @elseif($isLowStock)
                                        <span class="stock-state stock-state--low">Rendah</span>
                                    @else
                                        <span class="stock-state stock-state--safe">Aman</span>
                                    @endif
                                </div>
                                <div class="stock-progress" aria-label="Indikator stok">
                                    <div class="stock-progress__bar {{ $isOutOfStock ? 'stock-progress__bar--out' : ($isLowStock ? 'stock-progress__bar--low' : '') }}" style="width:{{ $stockProgress }}%"></div>
                                </div>
                                <div class="stock-variants">{{ $product->variants->count() }} varian · min. {{ $product->min_stock }}</div>
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="status-pill status-pill--active">Aktif</span>
                                @else
                                    <span class="status-pill status-pill--inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="product-actions">
                                    <a href="{{ route('inventory.products.show', $product) }}" class="product-action" title="Lihat detail {{ $product->name }}" aria-label="Lihat detail {{ $product->name }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.8 12s3.2-5.2 9.2-5.2 9.2 5.2 9.2 5.2-3.2 5.2-9.2 5.2S2.8 12 2.8 12Z" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.4"/></svg>
                                    </a>
                                    <a href="{{ route('inventory.products.edit', $product) }}" class="product-action product-action--edit" title="Edit {{ $product->name }}" aria-label="Edit {{ $product->name }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m14 5 5 5M4 20l1.2-5.2L16.5 3.5a2.1 2.1 0 0 1 3 3L8.2 17.8 4 20Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="products-empty">
                                <div class="products-empty__icon">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m4 7 8-4 8 4-8 4-8-4Z" stroke-linejoin="round"/><path d="m4 7 8 4 8-4v10l-8 4-8-4V7Z" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="products-empty__title">Produk tidak ditemukan</div>
                                <p class="products-empty__text">Belum ada produk yang sesuai dengan filter ini. Ubah pencarian atau tambahkan produk baru ke katalog.</p>
                                <div class="products-empty__actions">
                                    @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                                        <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary">Reset Filter</a>
                                    @endif
                                    <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">Tambah Produk</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <footer class="product-data-card__footer">{{ $products->links() }}</footer>
        @endif
    </section>
</div>
@endsection
