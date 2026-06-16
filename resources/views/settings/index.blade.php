@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem & Toko')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Sistem</span>
        <span class="sep">›</span>
        <span>Pengaturan</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box slate">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1>Pengaturan Sistem & Toko</h1>
                <p class="subtitle">Konfigurasi profil bisnis, pajak, struk thermal, dan preferensi sistem lainnya.</p>
            </div>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: 240px 1fr; gap: 24px; align-items: start;">

    {{-- Sidebar Settings --}}
    <div class="card" style="position:sticky;top:88px;">
        <div class="card-header">
            <div class="card-title" style="font-size:12px;text-transform:uppercase;letter-spacing:0.6px;color:#64748B;">Menu Pengaturan</div>
        </div>
        <div class="card-body" style="padding:8px 0;">
            <a href="#store-profile" class="settings-nav-item active" style="padding:11px 16px;border-left:3px solid transparent;font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;text-decoration:none;transition:all 0.15s;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Profil Toko
            </a>
            <a href="#tax-setting" class="settings-nav-item" style="padding:11px 16px;border-left:3px solid transparent;font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;text-decoration:none;transition:all 0.15s;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                Pajak & Biaya
            </a>
            <a href="#receipt-setting" class="settings-nav-item" style="padding:11px 16px;border-left:3px solid transparent;font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;text-decoration:none;transition:all 0.15s;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Template Struk
            </a>
        </div>
    </div>

    {{-- Content Settings --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Profil Toko --}}
        <div class="card" id="store-profile">
            <div class="card-header">
                <div class="card-title">
                    <div class="section-title-dot"></div>
                    Profil Toko
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.store') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-2" style="gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Nama Toko *</label>
                            <input type="text" name="store_name" class="form-control" value="{{ $settings['store_name'] ?? 'FashionPOS' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon / WhatsApp</label>
                            <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] ?? '' }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="store_address" class="form-control" rows="3" placeholder="Jl. Contoh No. 1, Kota...">{{ $settings['store_address'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pajak --}}
        <div class="card" id="tax-setting">
            <div class="card-header">
                <div class="card-title">
                    <div class="section-title-dot"></div>
                    Pengaturan Pajak (PPN)
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.tax') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Persentase PPN (%)</label>
                        <div class="input-group">
                            <span class="input-addon">%</span>
                            <input type="number" name="tax_percent" class="form-control" value="{{ $settings['tax_percent'] ?? '0' }}" step="0.1" min="0" max="100" style="border-radius:0 var(--radius-md) var(--radius-md) 0;">
                        </div>
                        <span class="form-hint">Masukkan 0 jika toko Anda tidak memungut PPN.</span>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan Pajak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Receipt --}}
        <div class="card" id="receipt-setting">
            <div class="card-header">
                <div class="card-title">
                    <div class="section-title-dot"></div>
                    Template Struk Thermal
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.receipt') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Pesan Header (Atas Struk)</label>
                        <textarea name="receipt_header" class="form-control" rows="2" placeholder="Selamat Datang!">{{ $settings['receipt_header'] ?? 'Selamat Datang!' }}</textarea>
                        <span class="form-hint">Tampil di bagian atas struk setelah nama toko.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pesan Footer (Bawah Struk)</label>
                        <textarea name="receipt_footer" class="form-control" rows="2" placeholder="Terima kasih atas kunjungan Anda.">{{ $settings['receipt_footer'] ?? 'Terima Kasih atas kunjungan Anda.' }}</textarea>
                        <span class="form-hint">Tampil di bagian bawah struk setelah total pembayaran.</span>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan Template Struk
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
    .settings-nav-item {
        color: #374151;
        background: transparent;
    }
    .settings-nav-item:hover {
        background: #F8FAFC;
    }
    .settings-nav-item.active {
        border-left-color: #4F46E5 !important;
        background: #F0F4FF !important;
        color: #4F46E5 !important;
        font-weight: 700 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navItems = document.querySelectorAll('.settings-nav-item');
        
        // Handle click
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Update active state
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Set initial active state based on hash if present
        if(window.location.hash) {
            const activeLink = document.querySelector(`.settings-nav-item[href="${window.location.hash}"]`);
            if(activeLink) {
                navItems.forEach(nav => nav.classList.remove('active'));
                activeLink.classList.add('active');
            }
        }
    });
</script>

@endsection
