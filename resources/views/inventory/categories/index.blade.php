@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori Produk')

@push('styles')
<style>
    .categories-page {
        --category-primary: #4F46E5;
        --category-blue: #3B82F6;
        --category-success: #10B981;
        --category-violet: #8B5CF6;
    }

    .categories-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 18px;
        padding: 24px 26px;
        background:
            radial-gradient(circle at 88% 0%, rgba(139, 92, 246, .13), transparent 17rem),
            linear-gradient(120deg, #FFFFFF 0%, #FAFBFF 52%, #F5F3FF 100%);
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 10px 28px rgba(79,70,229,.045);
    }

    .categories-hero::after {
        content: '';
        width: 180px;
        height: 180px;
        position: absolute;
        top: -124px;
        right: 16%;
        border: 1px solid rgba(139, 92, 246, .13);
        border-radius: 50%;
        box-shadow: 0 0 0 34px rgba(255,255,255,.28), 0 0 0 68px rgba(79,70,229,.035);
        pointer-events: none;
    }

    .categories-hero__content { position: relative; z-index: 1; }

    .categories-breadcrumb {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 9px;
        color: #64748B;
        font-size: 11px;
        font-weight: 700;
    }

    .categories-breadcrumb a { color: #4F46E5; text-decoration: none; }
    .categories-breadcrumb svg { width: 12px; height: 12px; }

    .categories-title {
        color: #111827;
        font-family: var(--font-sans);
        font-size: clamp(25px, 2vw, 32px);
        font-weight: 800;
        letter-spacing: -.045em;
        line-height: 1.15;
    }

    .categories-subtitle { margin-top: 7px; color: #64748B; font-size: 13px; line-height: 1.65; }

    .categories-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        position: relative;
        z-index: 1;
        padding: 8px 12px;
        color: #4338CA;
        background: rgba(255,255,255,.80);
        border: 1px solid #C7D2FE;
        border-radius: 999px;
        box-shadow: 0 6px 16px rgba(79,70,229,.07);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .04em;
    }

    .categories-hero__badge::before { content: ''; width: 7px; height: 7px; background: #10B981; border-radius: 50%; box-shadow: 0 0 0 4px rgba(16,185,129,.12); }

    .category-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .category-summary {
        --summary-color: #4F46E5;
        --summary-soft: #EEF2FF;
        display: flex;
        align-items: center;
        gap: 13px;
        position: relative;
        overflow: hidden;
        padding: 16px;
        background: linear-gradient(145deg, #FFFFFF 52%, var(--summary-soft) 145%);
        border: 1px solid #E5E7EB;
        border-radius: 17px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 7px 20px rgba(15,23,42,.035);
        transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease;
    }

    .category-summary::before { content: ''; width: 36px; height: 3px; position: absolute; top: 0; left: 0; background: var(--summary-color); border-radius: 0 999px 999px 0; }
    .category-summary:hover { border-color: color-mix(in srgb, var(--summary-color) 34%, #E5E7EB); box-shadow: 0 13px 28px color-mix(in srgb, var(--summary-color) 9%, transparent); transform: translateY(-3px); }
    .category-summary--root { --summary-color: #3B82F6; --summary-soft: #EFF6FF; }
    .category-summary--child { --summary-color: #8B5CF6; --summary-soft: #F5F3FF; }
    .category-summary--active { --summary-color: #10B981; --summary-soft: #ECFDF5; }

    .category-summary__icon {
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

    .category-summary:hover .category-summary__icon { transform: rotate(-4deg) scale(1.05); }
    .category-summary__icon svg { width: 20px; height: 20px; }
    .category-summary__value { display: block; color: #111827; font-family: var(--font-sans); font-size: 22px; font-weight: 800; letter-spacing: -.04em; line-height: 1; }
    .category-summary__label { display: block; margin-top: 5px; color: #64748B; font-size: 10px; font-weight: 800; letter-spacing: .055em; text-transform: uppercase; }

    .category-workspace { display: grid; grid-template-columns: minmax(0, 1fr) 350px; gap: 18px; align-items: start; }

    .category-list-card,
    .category-form-card {
        overflow: hidden;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 19px;
        box-shadow: 0 1px 2px rgba(15,23,42,.025), 0 10px 26px rgba(15,23,42,.045);
    }

    .category-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        min-height: 66px;
        padding: 14px 17px;
        background: #FFFFFF;
        border-bottom: 1px solid #E5E7EB;
    }

    .category-card-title { color: #111827; font-size: 13px; font-weight: 800; }
    .category-card-caption { margin-top: 3px; color: #64748B; font-size: 10px; }

    .category-search { width: min(270px, 100%); }
    .category-search .form-control { height: 38px; padding-left: 39px; background: #F8FAFC; border-radius: 11px; font-size: 11px; }
    .category-search .search-input-icon { left: 13px; color: #4F46E5; }

    .category-table-scroll { overflow: auto; }
    .category-table { min-width: 690px; border-collapse: separate; border-spacing: 0; }
    .category-table thead th { height: 45px; padding: 0 15px; color: #475569; background: #F8FAFC; border-bottom: 1px solid #E5E7EB; font-size: 9.5px; font-weight: 800; letter-spacing: .075em; }
    .category-table tbody tr { border: 0; transition: background .22s ease, box-shadow .22s ease; }
    .category-table tbody tr:hover { background: #F7F9FF; box-shadow: inset 3px 0 0 #4F46E5; }
    .category-table tbody td { height: 64px; padding: 10px 15px; color: #475569; border-bottom: 1px solid #EEF2F7; font-size: 12px; vertical-align: middle; }
    .category-table tbody tr:last-child td { border-bottom: 0; }

    .category-row--child { background: #FCFDFF; }
    .category-row--child td:first-child { position: relative; padding-left: 49px; }
    .category-row--child td:first-child::before { content: ''; width: 16px; height: 25px; position: absolute; left: 25px; top: 0; border-bottom: 1.5px solid #C7D2FE; border-left: 1.5px solid #C7D2FE; border-radius: 0 0 0 7px; }

    .category-identity { display: flex; align-items: center; gap: 11px; min-width: 0; }
    .category-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        background: linear-gradient(145deg, #EEF2FF, #EFF6FF);
        border: 1px solid #C7D2FE;
        border-radius: 11px;
        box-shadow: 0 4px 12px rgba(79,70,229,.07);
        font-size: 18px;
        transition: transform .22s ease;
    }

    .category-row--child .category-icon { width: 34px; height: 34px; background: #F8FAFC; border-color: #E2E8F0; font-size: 16px; }
    .category-table tbody tr:hover .category-icon { transform: rotate(-4deg) scale(1.05); }
    .category-name { overflow: hidden; color: #111827; font-size: 12.5px; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
    .category-kind { margin-top: 3px; color: #64748B; font-size: 9.5px; }

    .children-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; color: #4338CA; background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 999px; font-size: 9.5px; font-weight: 800; }
    .children-pill--empty { color: #64748B; background: #F8FAFC; border-color: #E5E7EB; }

    .category-status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border: 1px solid; border-radius: 999px; font-size: 9.5px; font-weight: 800; }
    .category-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
    .category-status--active { color: #047857; background: #ECFDF5; border-color: #A7F3D0; }
    .category-status--active::before { background: #10B981; box-shadow: 0 0 0 3px rgba(16,185,129,.11); }
    .category-status--inactive { color: #64748B; background: #F8FAFC; border-color: #E2E8F0; }
    .category-status--inactive::before { background: #94A3B8; }

    .category-action {
        width: 35px;
        height: 35px;
        display: grid;
        place-items: center;
        color: #DC2626;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        box-shadow: 0 2px 7px rgba(15,23,42,.035);
        cursor: pointer;
        transition: transform .2s ease, color .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .category-action svg { width: 15px; height: 15px; }
    .category-action:hover { color: #B91C1C; background: #FEF2F2; border-color: #FCA5A5; box-shadow: 0 8px 16px rgba(239,68,68,.11); transform: translateY(-2px); }
    .category-action:disabled { color: #94A3B8; background: #F8FAFC; cursor: not-allowed; opacity: .65; }
    .category-action:disabled:hover { border-color: #E5E7EB; box-shadow: none; transform: none; }

    .category-no-results { display: none; padding: 42px 20px !important; text-align: center; }
    .category-no-results__icon { width: 54px; height: 54px; display: grid; place-items: center; margin: 0 auto 11px; color: #4F46E5; background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 16px; }
    .category-no-results__title { color: #111827; font-weight: 800; }
    .category-no-results__text { margin-top: 5px; color: #64748B; font-size: 10.5px; }

    .category-empty { padding: 55px 20px !important; text-align: center; }
    .category-empty__icon { width: 62px; height: 62px; display: grid; place-items: center; margin: 0 auto 13px; color: #4F46E5; background: linear-gradient(145deg, #EEF2FF, #F5F3FF); border: 1px solid #C7D2FE; border-radius: 19px; }
    .category-empty__title { color: #111827; font-size: 14px; font-weight: 800; }
    .category-empty__text { margin-top: 5px; color: #64748B; font-size: 11px; }

    .category-form-card { position: sticky; top: 88px; }
    .category-form-card .category-card-header { background: linear-gradient(135deg, #FFFFFF, #FAF9FF); }
    .category-form-body { padding: 18px; }

    .category-form-intro { display: flex; align-items: center; gap: 11px; margin-bottom: 18px; padding: 12px; background: linear-gradient(135deg, #EEF2FF, #F5F3FF); border: 1px solid #DDD6FE; border-radius: 14px; }
    .category-icon-preview { width: 42px; height: 42px; flex: 0 0 auto; display: grid; place-items: center; background: #FFFFFF; border: 1px solid #C7D2FE; border-radius: 12px; box-shadow: 0 5px 13px rgba(79,70,229,.09); font-size: 20px; }
    .category-form-intro strong { display: block; color: #312E81; font-size: 11.5px; }
    .category-form-intro span { display: block; margin-top: 2px; color: #6366F1; font-size: 9.5px; line-height: 1.5; }

    .category-form-body .form-group { margin-bottom: 15px; }
    .category-form-body .form-label { color: #374151; font-size: 11.5px; font-weight: 800; }
    .category-form-body .form-control { height: 45px; background: #F8FAFC; border-radius: 12px; }
    .category-form-body .form-control:focus { background: #FFFFFF; }
    .category-form-hint { display: block; margin-top: 6px; color: #64748B; font-size: 9.5px; line-height: 1.5; }
    .category-submit { min-height: 45px; margin-top: 3px; border-radius: 12px; }

    @media (max-width: 1100px) {
        .category-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .category-workspace { grid-template-columns: minmax(0, 1fr) 310px; }
    }

    @media (max-width: 850px) {
        .category-workspace { grid-template-columns: 1fr; }
        .category-form-card { position: static; }
    }

    @media (max-width: 650px) {
        .categories-hero { align-items: flex-start; flex-direction: column; padding: 21px; }
        .category-card-header { align-items: flex-start; flex-direction: column; }
        .category-search { width: 100%; }
    }

    @media (max-width: 480px) {
        .category-summary-grid { grid-template-columns: 1fr; }
        .categories-hero, .category-list-card, .category-form-card { border-radius: 16px; }
    }
</style>
@endpush

@section('content')
@php
    $rootCategoryCount = $categories->count();
    $childCategoryCount = $categories->sum('children_count');
    $totalCategoryCount = $rootCategoryCount + $childCategoryCount;
    $activeCategoryCount = $categories->where('is_active', true)->count()
        + $categories->sum(fn ($category) => $category->children->where('is_active', true)->count());
@endphp

<div class="categories-page">
    <header class="categories-hero">
        <div class="categories-hero__content">
            <nav class="categories-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Inventori</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span>Kategori</span>
            </nav>
            <h1 class="categories-title">Kategori Produk</h1>
            <p class="categories-subtitle">Susun kategori dan subkategori agar katalog tetap rapi dan mudah dikelola.</p>
        </div>
        <div class="categories-hero__badge">Struktur katalog aktif</div>
    </header>

    <section class="category-summary-grid" aria-label="Ringkasan kategori">
        <div class="category-summary">
            <span class="category-summary__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h.01M4 3h7l9 9-8 8-9-9V4a1 1 0 0 1 1-1Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="category-summary__value">{{ $totalCategoryCount }}</span><span class="category-summary__label">Total Kategori</span></span>
        </div>
        <div class="category-summary category-summary--root">
            <span class="category-summary__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3.5 6.5h7l2 2h8v10h-17v-12Z" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="category-summary__value">{{ $rootCategoryCount }}</span><span class="category-summary__label">Kategori Utama</span></span>
        </div>
        <div class="category-summary category-summary--child">
            <span class="category-summary__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 4v8a4 4 0 0 0 4 4h8m-3-3 3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="category-summary__value">{{ $childCategoryCount }}</span><span class="category-summary__label">Subkategori</span></span>
        </div>
        <div class="category-summary category-summary--active">
            <span class="category-summary__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.2 2.2 4.8-4.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span><span class="category-summary__value">{{ $activeCategoryCount }}</span><span class="category-summary__label">Kategori Aktif</span></span>
        </div>
    </section>

    <div class="category-workspace">
        <section class="category-list-card" aria-label="Daftar kategori">
            <header class="category-card-header">
                <div>
                    <div class="category-card-title">Daftar Kategori</div>
                    <div class="category-card-caption">{{ $totalCategoryCount }} kategori dalam {{ $rootCategoryCount }} kelompok utama</div>
                </div>
                <div class="search-input category-search">
                    <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m21 21-4.4-4.4M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" stroke-linecap="round"/></svg>
                    <input type="search" class="form-control" id="categorySearch" placeholder="Cari kategori..." autocomplete="off">
                </div>
            </header>
            <div class="category-table-scroll">
                <table class="category-table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th width="140">Hierarchy</th>
                            <th width="115">Status</th>
                            <th width="75">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        @forelse($categories as $category)
                            <tr class="category-row category-row--parent" data-category-group="{{ $category->id }}" data-category-search="{{ Str::lower($category->name.' '.$category->children->pluck('name')->join(' ')) }}">
                                <td>
                                    <div class="category-identity">
                                        <span class="category-icon">{{ $category->icon ?? '📁' }}</span>
                                        <span><span class="category-name">{{ $category->name }}</span><span class="category-kind">Kategori utama</span></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="children-pill {{ $category->children_count === 0 ? 'children-pill--empty' : '' }}">
                                        {{ $category->children_count }} subkategori
                                    </span>
                                </td>
                                <td>
                                    <span class="category-status {{ $category->is_active ? 'category-status--active' : 'category-status--inactive' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td>
                                    @if($category->children_count > 0)
                                        <button type="button" class="category-action" disabled title="Hapus subkategori terlebih dahulu" aria-label="Kategori memiliki subkategori dan tidak dapat dihapus">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    @else
                                        <form action="{{ route('inventory.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="category-action" title="Hapus {{ $category->name }}" aria-label="Hapus {{ $category->name }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @foreach($category->children as $child)
                                <tr class="category-row category-row--child" data-category-group="{{ $category->id }}" data-category-search="{{ Str::lower($child->name.' '.$category->name) }}">
                                    <td>
                                        <div class="category-identity">
                                            <span class="category-icon">{{ $child->icon ?? '🏷️' }}</span>
                                            <span><span class="category-name">{{ $child->name }}</span><span class="category-kind">Di bawah {{ $category->name }}</span></span>
                                        </div>
                                    </td>
                                    <td><span class="children-pill children-pill--empty">Subkategori</span></td>
                                    <td><span class="category-status {{ $child->is_active ? 'category-status--active' : 'category-status--inactive' }}">{{ $child->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td>
                                        <form action="{{ route('inventory.categories.destroy', $child) }}" method="POST" onsubmit="return confirm('Hapus subkategori ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="category-action" title="Hapus {{ $child->name }}" aria-label="Hapus {{ $child->name }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="category-empty">
                                    <div class="category-empty__icon">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 7h.01M4 3h7l9 9-8 8-9-9V4a1 1 0 0 1 1-1Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div class="category-empty__title">Belum ada kategori</div>
                                    <div class="category-empty__text">Gunakan form di samping untuk membuat kategori pertama.</div>
                                </td>
                            </tr>
                        @endforelse
                        @if($categories->isNotEmpty())
                            <tr id="categoryNoResults">
                                <td colspan="4" class="category-no-results">
                                    <div class="category-no-results__icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m21 21-4.4-4.4M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" stroke-linecap="round"/></svg>
                                    </div>
                                    <div class="category-no-results__title">Kategori tidak ditemukan</div>
                                    <div class="category-no-results__text">Coba gunakan kata kunci pencarian lain.</div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="category-form-card" aria-label="Tambah kategori">
            <header class="category-card-header">
                <div>
                    <div class="category-card-title">Tambah Kategori</div>
                    <div class="category-card-caption">Buat kelompok produk baru</div>
                </div>
            </header>
            <div class="category-form-body">
                <div class="category-form-intro">
                    <span class="category-icon-preview" id="categoryIconPreview">🏷️</span>
                    <span><strong>Identitas kategori</strong><span>Gunakan nama dan ikon yang mudah dikenali tim.</span></span>
                </div>
                <form action="{{ route('inventory.categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="categoryName">Nama Kategori <span style="color:var(--color-danger)">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control {{ $errors->has('name') ? 'error' : '' }}" required placeholder="Contoh: Atasan" value="{{ old('name') }}" maxlength="100" autocomplete="off">
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="categoryParent">Induk Kategori</label>
                        <select name="parent_id" id="categoryParent" class="form-control">
                            <option value="">Sebagai Kategori Utama</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->icon ?? '📁' }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <span class="category-form-hint">Kosongkan untuk membuat kategori tingkat utama.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="categoryIcon">Ikon Emoji</label>
                        <input type="text" name="icon" id="categoryIcon" class="form-control" placeholder="👕, 👖, 👗" value="{{ old('icon') }}" maxlength="10" autocomplete="off">
                        <span class="category-form-hint">Opsional, maksimal satu emoji sederhana.</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block category-submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const searchInput = document.getElementById('categorySearch');
        const rows = Array.from(document.querySelectorAll('.category-row'));
        const noResults = document.getElementById('categoryNoResults');
        const iconInput = document.getElementById('categoryIcon');
        const iconPreview = document.getElementById('categoryIconPreview');

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim().toLocaleLowerCase('id-ID');
                const groups = [...new Set(rows.map(row => row.dataset.categoryGroup))];
                let visibleGroups = 0;

                groups.forEach(group => {
                    const groupRows = rows.filter(row => row.dataset.categoryGroup === group);
                    const matches = !query || groupRows.some(row => row.dataset.categorySearch.includes(query));
                    groupRows.forEach(row => row.style.display = matches ? '' : 'none');
                    if (matches) visibleGroups++;
                });

                if (noResults) noResults.querySelector('td').style.display = visibleGroups === 0 ? 'table-cell' : 'none';
            });
        }

        if (iconInput && iconPreview) {
            const updatePreview = () => iconPreview.textContent = iconInput.value.trim() || '🏷️';
            iconInput.addEventListener('input', updatePreview);
            updatePreview();
        }
    })();
</script>
@endpush
