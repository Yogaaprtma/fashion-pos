@extends('layouts.app')

@section('title', 'Edit Promosi')
@section('page-title', 'Edit Promosi: ' . $promotion->name)

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-header">
        <div class="card-title">Pembaruan Event Promosi & Voucher</div>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.promotions.update', $promotion) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-2" style="gap:16px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Nama Promosi *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $promotion->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Voucher / Kupon</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $promotion->code) }}" placeholder="Contoh: DISKON10 (Kosongkan jika otomatis)">
                    <small style="color:var(--text-muted)">Jika diisi, pelanggan harus memasukkan kode ini di kasir untuk mendapatkan diskon.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Promosi *</label>
                    <select name="type" id="promoType" class="form-control" required onchange="togglePromoType()">
                        <option value="discount_percent" {{ $promotion->type === 'discount_percent' ? 'selected' : '' }}>Potongan Persen (%)</option>
                        <option value="discount_fixed" {{ $promotion->type === 'discount_fixed' ? 'selected' : '' }}>Potongan Nominal (Rupiah)</option>
                        <option value="bogo" {{ $promotion->type === 'bogo' ? 'selected' : '' }}>Beli 1 Gratis 1 (BOGO)</option>
                        <option value="bundling" {{ $promotion->type === 'bundling' ? 'selected' : '' }}>Paket Bundling</option>
                    </select>
                </div>
                <div class="form-group" id="promoValueGroup" style="{{ in_array($promotion->type, ['bogo', 'bundling']) ? 'display:none;' : '' }}">
                    <label class="form-label" id="valueLabel">
                        {{ $promotion->type === 'discount_percent' ? 'Persentase Diskon (%) *' : 'Nominal Potongan (Rp) *' }}
                    </label>
                    <input type="number" name="value" id="promoValue" class="form-control" min="0" value="{{ (int)$promotion->value }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Persyaratan Belanja *</label>
                    <select name="min_requirement_type" id="reqType" class="form-control" required onchange="toggleRequirement()">
                        <option value="none" {{ $promotion->min_requirement_type === 'none' ? 'selected' : '' }}>Tanpa Syarat</option>
                        <option value="min_spend" {{ $promotion->min_requirement_type === 'min_spend' ? 'selected' : '' }}>Minimal Nominal Belanja (Rp)</option>
                        <option value="min_qty" {{ $promotion->min_requirement_type === 'min_qty' ? 'selected' : '' }}>Minimal Jumlah Barang (Pcs)</option>
                    </select>
                </div>
                <div class="form-group" id="reqValueGroup" style="{{ $promotion->min_requirement_type === 'none' ? 'display:none;' : '' }}">
                    <label class="form-label" id="reqValueLabel">
                        {{ $promotion->min_requirement_type === 'min_spend' ? 'Minimal Belanja (Rupiah) *' : 'Minimal Jumlah Barang (Pcs) *' }}
                    </label>
                    <input type="number" name="min_requirement_value" id="reqValue" class="form-control" min="0" value="{{ (int)$promotion->min_requirement_value }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="datetime-local" name="start_date" class="form-control" required value="{{ $promotion->start_date->format('Y-m-d\TH:i') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="datetime-local" name="end_date" class="form-control" required value="{{ $promotion->end_date->format('Y-m-d\TH:i') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Batas Penggunaan Total</label>
                    <input type="number" name="usage_limit" class="form-control" value="{{ $promotion->usage_limit }}" placeholder="Contoh: 100 (Kosongkan jika tak terbatas)">
                    <small style="color:var(--text-muted)">Total kuota penggunaan voucher di toko.</small>
                </div>
                <div class="form-group" style="display:flex; align-items:center; margin-top:28px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $promotion->is_active ? 'checked' : '' }} style="width:20px; height:20px;">
                        <span style="font-weight:600;">Aktifkan Promosi Sekarang</span>
                    </label>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Target Produk *</label>
                    <select name="target_type" id="targetType" class="form-control" required onchange="toggleTargets()">
                        <option value="all" {{ $promotion->target_type === 'all' ? 'selected' : '' }}>Semua Produk</option>
                        <option value="category" {{ $promotion->target_type === 'category' ? 'selected' : '' }}>Kategori Tertentu</option>
                        <option value="product" {{ $promotion->target_type === 'product' ? 'selected' : '' }}>Produk Tertentu</option>
                    </select>
                </div>

                <!-- Category Targets -->
                <div class="form-group" id="categoryTargetGroup" style="grid-column: 1/-1; display:{{ $promotion->target_type === 'category' ? 'block' : 'none' }};">
                    <label class="form-label">Pilih Kategori Target *</label>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; max-height:150px; overflow-y:auto; padding:12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px;">
                        @foreach($categories as $cat)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="target_ids[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedTargets) ? 'checked' : '' }}>
                                <span>{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Product Targets -->
                <div class="form-group" id="productTargetGroup" style="grid-column: 1/-1; display:{{ $promotion->target_type === 'product' ? 'block' : 'none' }};">
                    <label class="form-label">Pilih Produk Target *</label>
                    <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; max-height:200px; overflow-y:auto; padding:12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px;">
                        @foreach($products as $prod)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="target_ids[]" value="{{ $prod->id }}" {{ in_array($prod->id, $selectedTargets) ? 'checked' : '' }}>
                                <span>{{ $prod->name }} (SKU: {{ $prod->sku }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Keterangan / Deskripsi Promosi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan internal promosi...">{{ old('description', $promotion->description) }}</textarea>
                </div>
            </div>

            <div style="margin-top:32px; display:flex; justify-content:flex-end; gap:12px">
                <a href="{{ route('inventory.promotions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePromoType() {
        const type = document.getElementById('promoType').value;
        const valueGroup = document.getElementById('promoValueGroup');
        const valueLabel = document.getElementById('valueLabel');
        const valueInput = document.getElementById('promoValue');

        if(type === 'discount_percent') {
            valueGroup.style.display = 'block';
            valueLabel.innerText = 'Persentase Diskon (%) *';
            valueInput.placeholder = 'Contoh: 10';
            valueInput.required = true;
        } else if(type === 'discount_fixed') {
            valueGroup.style.display = 'block';
            valueLabel.innerText = 'Nominal Potongan (Rp) *';
            valueInput.placeholder = 'Contoh: 50000';
            valueInput.required = true;
        } else {
            valueGroup.style.display = 'none';
            valueInput.required = false;
        }
    }

    function toggleRequirement() {
        const type = document.getElementById('reqType').value;
        const valGroup = document.getElementById('reqValueGroup');
        const valLabel = document.getElementById('reqValueLabel');
        const valInput = document.getElementById('reqValue');

        if(type === 'none') {
            valGroup.style.display = 'none';
            valInput.required = false;
        } else {
            valGroup.style.display = 'block';
            valInput.required = true;
            if(type === 'min_spend') {
                valLabel.innerText = 'Minimal Belanja (Rupiah) *';
                valInput.placeholder = 'Contoh: 100000';
            } else {
                valLabel.innerText = 'Minimal Jumlah Barang (Pcs) *';
                valInput.placeholder = 'Contoh: 3';
            }
        }
    }

    function toggleTargets() {
        const type = document.getElementById('targetType').value;
        const catGroup = document.getElementById('categoryTargetGroup');
        const prodGroup = document.getElementById('productTargetGroup');

        catGroup.style.display = 'none';
        prodGroup.style.display = 'none';

        if(type === 'category') {
            catGroup.style.display = 'block';
        } else if(type === 'product') {
            prodGroup.style.display = 'block';
        }
    }
</script>
@endsection
