@extends('layouts.app')

@section('title', 'Cetak Label Barcode')
@section('page-title', 'Cetak Label Price Tag')

@section('content')

<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Inventori</span>
        <span class="sep">›</span>
        <span>Cetak Label Barcode</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box indigo">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <h1>Cetak Label Price Tag / Barcode</h1>
                <p class="subtitle">Pilih produk & varian, tentukan jumlah cetakan, lalu cetak label ke printer thermal.</p>
            </div>
        </div>
    </div>
</div>

{{-- Print Form --}}
<form id="printForm" method="POST" action="{{ route('inventory.barcode-labels.print') }}" target="_blank">
@csrf

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- Left: Product List --}}
    <div>
        {{-- Search --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-body" style="padding:14px 16px;">
                <form method="GET" action="{{ route('inventory.barcode-labels.index') }}" style="display:flex;gap:10px;">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="🔍 Cari produk / SKU..." style="flex:1;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if($search)
                    <a href="{{ route('inventory.barcode-labels.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Product Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilih Varian Produk</h3>
                <div style="font-size:12px;color:var(--text-muted)">Centang varian yang ingin dicetak labelnya</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="selectAll" title="Pilih Semua">
                                </th>
                                <th>Produk / SKU</th>
                                <th>Varian</th>
                                <th>Harga Jual</th>
                                <th style="width:90px;">Jml Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="variant-check" data-id="{{ $variant->id }}"
                                            data-name="{{ $product->name }}" data-variant="{{ $variant->variant_label }}"
                                            data-sku="{{ $variant->sku }}" data-price="{{ $variant->selling_price }}">
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">{{ $product->name }}</div>
                                        <div style="font-size:11px;color:var(--text-muted);">{{ $variant->sku }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $variant->variant_label }}</span>
                                    </td>
                                    <td><strong>Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <input type="number" class="form-control qty-input" style="height:30px;font-size:12px;width:70px;"
                                            value="1" min="1" max="100" data-id="{{ $variant->id }}">
                                    </td>
                                </tr>
                                @endforeach
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada produk ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($products->hasPages())
            <div class="card-footer">
                {{ $products->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Right: Print Settings --}}
    <div>
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-header">
                <h3 class="card-title">⚙️ Pengaturan Cetak</h3>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">

                {{-- Selected Count --}}
                <div style="padding:10px 14px;background:rgba(79,70,229,0.08);border:1px solid rgba(79,70,229,0.2);border-radius:10px;text-align:center;">
                    <div style="font-size:24px;font-weight:800;color:#4F46E5;" id="selectedCount">0</div>
                    <div style="font-size:12px;color:var(--text-muted)">varian dipilih</div>
                </div>

                {{-- Label Size --}}
                <div>
                    <label class="form-label">Ukuran Label</label>
                    <select name="label_size" class="form-select">
                        <option value="50x30">50mm × 30mm (Standard)</option>
                        <option value="40x20">40mm × 20mm (Compact)</option>
                        <option value="60x40">60mm × 40mm (Besar)</option>
                    </select>
                </div>

                {{-- Options --}}
                <div>
                    <label class="form-label">Tampilkan pada Label</label>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="show_barcode" value="1" checked> Kode Barcode
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="show_price" value="1" checked> Harga Jual
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="show_variant" value="1" checked> Info Varian (Ukuran/Warna)
                        </label>
                    </div>
                </div>

                {{-- Print Button --}}
                <button type="button" id="printBtn" class="btn btn-primary" style="width:100%;height:44px;" onclick="submitPrint()">
                    🖨️ Cetak Label
                </button>

                {{-- Hidden variant inputs will be added here by JS --}}
                <div id="hiddenInputs"></div>

            </div>
        </div>
    </div>

</div>
</form>

<script>
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.variant-check').forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    document.querySelectorAll('.variant-check').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    function updateCount() {
        const count = document.querySelectorAll('.variant-check:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    function submitPrint() {
        const checked = document.querySelectorAll('.variant-check:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu varian produk!');
            return;
        }

        const container = document.getElementById('hiddenInputs');
        container.innerHTML = '';

        checked.forEach((cb, idx) => {
            const variantId = cb.dataset.id;
            const qtyInput = document.querySelector(`.qty-input[data-id="${variantId}"]`);
            const copies = qtyInput ? parseInt(qtyInput.value) : 1;

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `variants[${idx}][variant_id]`;
            idInput.value = variantId;

            const copiesInput = document.createElement('input');
            copiesInput.type = 'hidden';
            copiesInput.name = `variants[${idx}][copies]`;
            copiesInput.value = copies;

            container.appendChild(idInput);
            container.appendChild(copiesInput);
        });

        document.getElementById('printForm').submit();
    }
</script>

@endsection
