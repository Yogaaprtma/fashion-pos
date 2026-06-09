@extends('layouts.app')

@section('title', 'Barcode Generator')
@section('page-title', 'Cetak Label Barcode')

@section('content')
<div class="card mb-4 no-print">
    <div class="card-body">
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
            Pilih produk dan varian yang ingin dicetak label barcodenya. Label ini bisa ditempelkan langsung ke barang (baju/celana/aksesoris) agar mudah di-scan oleh kasir.
        </p>

        <form action="#" method="GET" style="display:flex; gap:16px; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1">
                <label class="form-label">Pilih Produk & Varian</label>
                <select name="variant_id" class="form-control" id="variantSelect">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($products as $prod)
                        <optgroup label="{{ $prod->name }}">
                            @foreach($prod->variants as $var)
                                <option value="{{ $var->sku_variant }}" data-name="{{ $prod->name }}" data-label="{{ $var->variant_label }}" data-price="{{ $var->effective_sell_price }}">
                                    {{ $prod->name }} - {{ $var->variant_label }} (Stok: {{ $var->stock_qty }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0; width:120px;">
                <label class="form-label">Jumlah Cetak</label>
                <input type="number" id="printCount" class="form-control" value="10" min="1" max="100">
            </div>
            <button type="button" class="btn btn-primary" onclick="generateBarcodePreviews()">Generate Preview</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()">🖨️ Cetak</button>
        </form>
    </div>
</div>

<div class="card print-area">
    <div class="card-header no-print">
        <div class="card-title">Preview Label</div>
    </div>
    <div class="card-body">
        <div id="barcodeContainer" style="display:flex; flex-wrap:wrap; gap:16px; justify-content:center;">
            <div style="text-align:center; padding:40px; color:var(--text-muted); width:100%;">
                Pilih barang dan klik Generate Preview.
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
        .no-print { display: none !important; }
    }
    .barcode-sticker {
        width: 160px;
        height: 100px;
        border: 1px dashed #ccc;
        padding: 8px;
        text-align: center;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
    }
    .barcode-sticker .shop-name { font-weight: bold; font-size: 11px; margin-bottom: 2px; }
    .barcode-sticker .item-name { font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width:100%; margin-bottom: 2px; }
    .barcode-sticker .price { font-weight: 800; font-size: 13px; }
    .barcode-sticker img { max-width: 100%; height: 35px; object-fit: contain; }
</style>

<script>
    function generateBarcodePreviews() {
        const select = document.getElementById('variantSelect');
        const count = parseInt(document.getElementById('printCount').value);
        const container = document.getElementById('barcodeContainer');
        
        if(!select.value) {
            alert('Pilih varian produk terlebih dahulu.');
            return;
        }
        
        const option = select.options[select.selectedIndex];
        const barcodeValue = select.value;
        const name = option.getAttribute('data-name');
        const label = option.getAttribute('data-label');
        const price = parseFloat(option.getAttribute('data-price')).toLocaleString('id-ID');
        
        container.innerHTML = '';
        
        for(let i = 0; i < count; i++) {
            const sticker = document.createElement('div');
            sticker.className = 'barcode-sticker';
            
            // Using a public barcode generator API just for preview visualization.
            // In a real app, we use picqer/php-barcode-generator package returning base64.
            const barcodeUrl = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${barcodeValue}&scale=2&height=10&includetext=true`;
            
            sticker.innerHTML = `
                <div class="shop-name">FASHION POS</div>
                <div class="item-name">${name} (${label})</div>
                <img src="${barcodeUrl}" alt="${barcodeValue}">
                <div class="price">Rp ${price}</div>
            `;
            container.appendChild(sticker);
        }
    }
</script>
@endsection
