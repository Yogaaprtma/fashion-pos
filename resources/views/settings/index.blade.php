@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem & Toko')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Pengaturan Sistem & Toko</h1>
        <p class="page-header-subtitle">Konfigurasi profil bisnis, pajak, struk, dan preferensi lainnya.</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 250px 1fr; gap: 24px; align-items: start;">
    
    <!-- Sidebar Settings -->
    <div class="card">
        <div class="card-body" style="padding: 12px 0;">
            <div style="padding: 8px 16px; font-weight: 600; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Menu Pengaturan</div>
            <a href="#store-profile" class="btn btn-block" style="text-align: left; background: var(--bg-elevated); border-radius: 0; padding: 12px 16px; color: var(--color-primary); border-left: 3px solid var(--color-primary);">Profil Toko</a>
            <a href="#tax-setting" class="btn btn-block" style="text-align: left; background: transparent; border-radius: 0; padding: 12px 16px; color: var(--text-primary); border-left: 3px solid transparent;">Pajak & Biaya</a>
            <a href="#receipt-setting" class="btn btn-block" style="text-align: left; background: transparent; border-radius: 0; padding: 12px 16px; color: var(--text-primary); border-left: 3px solid transparent;">Template Struk</a>
            <a href="#payment-methods" class="btn btn-block" style="text-align: left; background: transparent; border-radius: 0; padding: 12px 16px; color: var(--text-primary); border-left: 3px solid transparent;">Metode Pembayaran</a>
        </div>
    </div>

    <!-- Content Settings -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Profil Toko -->
        <div class="card" id="store-profile">
            <div class="card-header">
                <div class="card-title">Profil Toko</div>
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
                            <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone'] ?? '' }}">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="store_address" class="form-control" rows="3">{{ $settings['store_address'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pajak -->
        <div class="card" id="tax-setting">
            <div class="card-header">
                <div class="card-title">Pengaturan Pajak (PPN)</div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.tax') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Persentase PPN (%)</label>
                        <input type="number" name="tax_percent" class="form-control" value="{{ $settings['tax_percent'] ?? '0' }}" step="0.1" min="0" max="100">
                        <small style="color: var(--text-muted);">Masukkan 0 jika toko Anda tidak memungut PPN.</small>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">Simpan Pajak</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Receipt -->
        <div class="card" id="receipt-setting">
            <div class="card-header">
                <div class="card-title">Template Struk Thermal</div>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.receipt') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Pesan Header (Atas)</label>
                        <textarea name="receipt_header" class="form-control" rows="2">{{ $settings['receipt_header'] ?? 'Selamat Datang!' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pesan Footer (Bawah)</label>
                        <textarea name="receipt_footer" class="form-control" rows="2">{{ $settings['receipt_footer'] ?? 'Terima Kasih atas kunjungan Anda.' }}</textarea>
                    </div>
                    <div style="margin-top: 16px; text-align: right;">
                        <button type="submit" class="btn btn-primary">Simpan Template Struk</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
