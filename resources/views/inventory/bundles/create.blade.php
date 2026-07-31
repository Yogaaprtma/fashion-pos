@extends('layouts.app')

@section('title', 'Buat Paket Produk')
@section('page-title', 'Buat Paket Produk')

@section('content')

<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <a href="{{ route('inventory.bundles.index') }}">Produk Paket</a>
        <span class="sep">›</span>
        <span>Buat Baru</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box indigo">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <h1>Buat Paket Produk Baru</h1>
                <p class="subtitle">Gabungkan beberapa varian produk menjadi satu paket dengan harga spesial.</p>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('inventory.bundles.store') }}">
@csrf

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- Left: Bundle Items --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📦 Komposisi Paket</h3>
                <div style="font-size:12px;color:var(--text-muted);">Tambahkan produk/varian yang termasuk dalam paket ini</div>
            </div>
            <div class="card-body">

                {{-- Item Search --}}
                <div style="margin-bottom:14px;">
                    <input type="text" id="variantSearch" class="form-control" placeholder="🔍 Ketik nama produk / SKU untuk menambahkan..."
                        oninput="filterVariants(this.value)" autocomplete="off">
                    <div id="variantDropdown" style="display:none;border:1px solid var(--border);border-radius:10px;margin-top:4px;max-height:240px;overflow-y:auto;background:var(--bg-card);box-shadow:var(--shadow-lg);position:relative;z-index:50;"></div>
                </div>

                {{-- Selected Items Table --}}
                <div id="bundleItemsContainer">
                    <table class="table" style="font-size:13px;" id="bundleItemsTable">
                        <thead>
                            <tr>
                                <th>Produk / Varian</th>
                                <th>Harga Satuan</th>
                                <th style="width:90px;">Qty</th>
                                <th>Subtotal</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="bundleItemsBody">
                            <tr id="emptyRow">
                                <td colspan="5" class="text-center py-3 text-muted" style="font-style:italic;">
                                    Belum ada item. Cari produk di atas.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--bg-elevated);">
                                <td colspan="3" style="text-align:right;font-weight:700;font-size:13px;">Harga Normal Total:</td>
                                <td colspan="2"><strong id="normalTotalDisplay">Rp 0</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- Right: Bundle Info --}}
    <div style="position:sticky;top:80px;">
        <div class="card">
            <div class="card-header"><h3 class="card-title">⚙️ Info Paket</h3></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div>
                    <label class="form-label">Nama Paket <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Setelan Formal Kemeja + Celana" value="{{ old('name') }}">
                </div>

                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Opsional...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Tanpa Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pricing Summary --}}
                <div style="padding:12px 14px;background:rgba(79,70,229,0.06);border:1px solid rgba(79,70,229,0.15);border-radius:10px;">
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;font-weight:600;text-transform:uppercase;">Ringkasan Harga</div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                        <span>Harga Normal Total</span>
                        <span id="normalTotalSummary" style="text-decoration:line-through;color:var(--text-muted)">Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;">
                        <span>Harga Paket</span>
                        <strong id="bundlePriceSummary" style="color:#4F46E5;">Rp 0</strong>
                    </div>
                    <div id="discountBadge" style="display:none;text-align:center;margin-top:8px;">
                        <span class="badge badge-success" id="discountBadgeText"></span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Harga Paket (Rp) <span style="color:red">*</span></label>
                    <input type="number" name="bundle_price" id="bundlePriceInput" class="form-control" required min="0"
                        placeholder="0" value="{{ old('bundle_price') }}" oninput="updateSummary()">
                </div>

                <div>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Paket Aktif (muncul di kasir)
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;height:44px;">
                    💾 Simpan Paket
                </button>

                <a href="{{ route('inventory.bundles.index') }}" class="btn btn-secondary" style="width:100%;text-align:center;">
                    Batal
                </a>

            </div>
        </div>
    </div>

</div>
</form>

{{-- Hidden variant data for JS --}}
<script>
const allVariants = @json($variants->map(fn($v) => [
    'id' => $v->id,
    'name' => $v->product->name,
    'variant_label' => $v->variant_label,
    'sku' => $v->sku,
    'price' => (float)$v->selling_price,
]));

let bundleItems = []; // { variantId, name, variant_label, price, qty }
let itemIndex = 0;

function filterVariants(q) {
    const dd = document.getElementById('variantDropdown');
    if (!q || q.length < 2) { dd.style.display = 'none'; return; }
    const filtered = allVariants.filter(v =>
        v.name.toLowerCase().includes(q.toLowerCase()) ||
        v.sku.toLowerCase().includes(q.toLowerCase()) ||
        v.variant_label.toLowerCase().includes(q.toLowerCase())
    ).slice(0, 12);

    if (!filtered.length) { dd.style.display = 'none'; return; }

    dd.innerHTML = filtered.map(v => `
        <div onclick="addItem(${v.id})"
            style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--border);font-size:13px;"
            onmouseover="this.style.background='var(--bg-elevated)'"
            onmouseout="this.style.background=''">
            <strong>${v.name}</strong>
            <span style="color:var(--text-muted);margin-left:6px;">${v.variant_label}</span>
            <span style="float:right;color:#4F46E5;font-weight:700;">Rp ${v.price.toLocaleString('id-ID')}</span>
            <div style="font-size:10px;color:var(--text-muted);">${v.sku}</div>
        </div>
    `).join('');
    dd.style.display = 'block';
}

function addItem(variantId) {
    // Check duplicate
    if (bundleItems.find(i => i.variantId === variantId)) {
        alert('Varian ini sudah ada dalam paket!');
        document.getElementById('variantDropdown').style.display = 'none';
        return;
    }
    const v = allVariants.find(v => v.id === variantId);
    bundleItems.push({ variantId, name: v.name, variant_label: v.variant_label, price: v.price, qty: 1, idx: itemIndex++ });
    document.getElementById('variantSearch').value = '';
    document.getElementById('variantDropdown').style.display = 'none';
    renderTable();
}

function removeItem(variantId) {
    bundleItems = bundleItems.filter(i => i.variantId !== variantId);
    renderTable();
}

function qtyChange(variantId, val) {
    const item = bundleItems.find(i => i.variantId === variantId);
    if (item) { item.qty = Math.max(1, parseInt(val) || 1); }
    renderTable();
}

function renderTable() {
    const tbody = document.getElementById('bundleItemsBody');
    const emptyRow = document.getElementById('emptyRow');

    if (!bundleItems.length) {
        tbody.innerHTML = `<tr id="emptyRow"><td colspan="5" class="text-center py-3 text-muted" style="font-style:italic;">Belum ada item. Cari produk di atas.</td></tr>`;
        updateSummary();
        return;
    }

    tbody.innerHTML = bundleItems.map(item => `
        <tr>
            <td>
                <input type="hidden" name="items[${item.idx}][variant_id]" value="${item.variantId}">
                <div style="font-weight:600;">${item.name}</div>
                <div style="font-size:11px;color:var(--text-muted);">${item.variant_label}</div>
            </td>
            <td>Rp ${item.price.toLocaleString('id-ID')}</td>
            <td>
                <input type="number" name="items[${item.idx}][quantity]" value="${item.qty}" min="1" max="99"
                    class="form-control" style="height:30px;width:60px;font-size:12px;"
                    onchange="qtyChange(${item.variantId}, this.value)">
            </td>
            <td><strong>Rp ${(item.price * item.qty).toLocaleString('id-ID')}</strong></td>
            <td>
                <button type="button" onclick="removeItem(${item.variantId})"
                    style="background:none;border:none;color:var(--color-danger);cursor:pointer;font-size:18px;">✕</button>
            </td>
        </tr>
    `).join('');

    updateSummary();
}

function updateSummary() {
    const normalTotal = bundleItems.reduce((sum, i) => sum + (i.price * i.qty), 0);
    const bundlePrice = parseFloat(document.getElementById('bundlePriceInput').value) || 0;
    const discount = normalTotal > 0 ? ((normalTotal - bundlePrice) / normalTotal * 100).toFixed(1) : 0;

    document.getElementById('normalTotalDisplay').textContent = 'Rp ' + normalTotal.toLocaleString('id-ID');
    document.getElementById('normalTotalSummary').textContent = 'Rp ' + normalTotal.toLocaleString('id-ID');
    document.getElementById('bundlePriceSummary').textContent = 'Rp ' + bundlePrice.toLocaleString('id-ID');

    const badge = document.getElementById('discountBadge');
    if (discount > 0) {
        badge.style.display = 'block';
        document.getElementById('discountBadgeText').textContent = `Hemat ${discount}%`;
    } else {
        badge.style.display = 'none';
    }
}

// Close dropdown on outside click
document.addEventListener('click', e => {
    if (!e.target.closest('#variantSearch') && !e.target.closest('#variantDropdown')) {
        document.getElementById('variantDropdown').style.display = 'none';
    }
});
</script>

@endsection
