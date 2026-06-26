@extends('layouts.app')

@section('title', 'Tambah Promosi')
@section('page-title', 'Tambah Promosi Baru')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-header">
        <div class="card-title">Event Promosi & Voucher Baru</div>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.promotions.store') }}" method="POST">
            @csrf

            <div class="grid grid-2" style="gap:16px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Nama Promosi *</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Promo Gajian, Diskon Merdeka" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Voucher / Kupon</label>
                    <input type="text" name="code" class="form-control" placeholder="Contoh: DISKON10 (Kosongkan jika otomatis)">
                    <small style="color:var(--text-muted)">Jika diisi, pelanggan harus memasukkan kode ini di kasir untuk mendapatkan diskon.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Promosi *</label>
                    <select name="type" id="promoType" class="form-control" required onchange="togglePromoType()">
                        <option value="discount_percent">Potongan Persen (%)</option>
                        <option value="discount_fixed">Potongan Nominal (Rupiah)</option>
                        <option value="bogo">Beli 1 Gratis 1 (BOGO)</option>
                        <option value="bundling">Paket Bundling</option>
                    </select>
                </div>
                <div class="form-group" id="promoValueGroup">
                    <label class="form-label" id="valueLabel">Persentase Diskon (%) *</label>
                    <input type="number" name="value" id="promoValue" class="form-control" min="0" value="10">
                </div>

                <div class="form-group">
                    <label class="form-label">Persyaratan Belanja *</label>
                    <select name="min_requirement_type" id="reqType" class="form-control" required onchange="toggleRequirement()">
                        <option value="none">Tanpa Syarat</option>
                        <option value="min_spend">Minimal Nominal Belanja (Rp)</option>
                        <option value="min_qty">Minimal Jumlah Barang (Pcs)</option>
                    </select>
                </div>
                <div class="form-group" id="reqValueGroup" style="display:none">
                    <label class="form-label" id="reqValueLabel">Syarat Minimal *</label>
                    <input type="number" name="min_requirement_value" id="reqValue" class="form-control" min="0" value="0">
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="datetime-local" name="start_date" class="form-control" required value="{{ date('Y-m-d\TH:i') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="datetime-local" name="end_date" class="form-control" required value="{{ date('Y-m-d\TH:i', strtotime('+1 week')) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Batas Penggunaan Total</label>
                    <input type="number" name="usage_limit" class="form-control" placeholder="Contoh: 100 (Kosongkan jika tak terbatas)">
                    <small style="color:var(--text-muted)">Total kuota penggunaan voucher di toko.</small>
                </div>
                <div class="form-group" style="display:flex; align-items:center; margin-top:28px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                        <input type="checkbox" name="is_active" value="1" checked style="width:20px; height:20px;">
                        <span style="font-weight:600;">Aktifkan Promosi Sekarang</span>
                    </label>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Target Produk *</label>
                    <select name="target_type" id="targetType" class="form-control" required onchange="toggleTargets()">
                        <option value="all">Semua Produk</option>
                        <option value="category">Kategori Tertentu</option>
                        <option value="product">Produk Tertentu</option>
                    </select>
                </div>

                <!-- Category Targets -->
                <div class="form-group" id="categoryTargetGroup" style="grid-column: 1/-1; display:none;">
                    <label class="form-label">Pilih Kategori Target *</label>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; max-height:150px; overflow-y:auto; padding:12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px;">
                        @foreach($categories as $cat)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="target_ids[]" value="{{ $cat->id }}">
                                <span>{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Product Targets -->
                <div class="form-group" id="productTargetGroup" style="grid-column: 1/-1; display:none;">
                    <label class="form-label">Pilih Produk Target *</label>
                    <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; max-height:200px; overflow-y:auto; padding:12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px;">
                        @foreach($products as $prod)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="target_ids[]" value="{{ $prod->id }}">
                                <span>{{ $prod->name }} (SKU: {{ $prod->sku }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Keterangan / Deskripsi Promosi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan internal promosi..."></textarea>
                </div>
            </div>

            <div style="margin-top:32px; display:flex; justify-content:flex-end; gap:12px">
                <a href="{{ route('inventory.promotions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Promosi</button>
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

        // Uncheck all in both groups first
        document.querySelectorAll('#categoryTargetGroup input, #productTargetGroup input').forEach(input => {
            input.checked = false;
        });

        if(type === 'category') {
            catGroup.style.display = 'block';
        } else if(type === 'product') {
            prodGroup.style.display = 'block';
        }
    }
</script>
@endsection
