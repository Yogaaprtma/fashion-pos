@extends('layouts.app')

@section('title', 'Tambah Aset')
@section('page-title', 'Pendaftaran Aset Baru')

@section('content')

<div class="card" style="max-width:600px; margin:0 auto;">
    <div class="card-body">
        <form action="{{ route('assets.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Kode Aset (Otomatis jika kosong)</label>
                <input type="text" name="asset_code" class="form-control" placeholder="AST-2026...">
            </div>
            
            <div class="form-group">
                <label class="form-label">Nama Aset *</label>
                <input type="text" name="name" class="form-control" required placeholder="Mesin Kasir POS Utama">
            </div>
            
            <div class="grid grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="asset_category_id" class="form-control" required>
                        <option value="">Pilih Kategori...</option>
                        @foreach(\App\Models\AssetCategory::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kondisi Aset *</label>
                    <select name="condition" class="form-control" required>
                        <option value="good">Baik</option>
                        <option value="fair">Cukup (Perlu Perawatan)</option>
                        <option value="poor">Rusak Ringan/Berat</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">Nilai Pembelian Awal (Rp) *</label>
                    <input type="number" name="purchase_price" class="form-control" required min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Saat Ini (Rp) *</label>
                    <input type="number" name="current_value" class="form-control" required min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Lokasi / Penempatan</label>
                <input type="text" name="location" class="form-control" placeholder="Lantai 1 - Area Kasir">
            </div>
            
            <div class="form-group">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div style="margin-top:24px; text-align:right;">
                <a href="{{ route('assets.index') }}" class="btn btn-secondary" style="margin-right:8px;">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Aset</button>
            </div>
        </form>
    </div>
</div>

@endsection
