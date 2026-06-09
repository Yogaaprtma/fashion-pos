@extends('layouts.app')

@section('title', 'Buat Purchase Order')
@section('page-title', 'Buat Purchase Order (PO) Baru')

@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('purchase.orders.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-2" style="gap:20px; margin-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Pilih Supplier *</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach(\App\Models\Supplier::all() as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan PO</label>
                    <input type="text" name="notes" class="form-control" placeholder="Instruksi pengiriman...">
                </div>
            </div>

            <hr style="margin-bottom:24px; border-color:var(--border);">

            <div class="flex-between" style="margin-bottom:16px">
                <div class="card-title">Daftar Barang (Item PO)</div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addPoItem()">+ Tambah Barang</button>
            </div>

            <div id="poItemsContainer">
                <!-- Row 1 -->
                <div class="po-row" style="display:grid; grid-template-columns: 2fr 1fr 1fr 40px; gap:10px; margin-bottom:12px; align-items:end;">
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Produk & Varian *</label>
                        <select name="items[0][product_variant_id]" class="form-control" required>
                            <option value="">Pilih Barang...</option>
                            @foreach(\App\Models\ProductVariant::with('product')->get() as $pv)
                                <option value="{{ $pv->id }}">{{ $pv->product?->name }} - {{ $pv->variant_label }} (Stok: {{ $pv->stock_qty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Harga Beli Satuan (Rp) *</label>
                        <input type="number" name="items[0][unit_price]" class="form-control" min="0" required>
                    </div>
                    <div class="form-group" style="margin:0">
                        <label class="form-label">Qty Pesan *</label>
                        <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" required>
                    </div>
                    <button type="button" class="btn btn-secondary btn-icon" onclick="this.parentElement.remove()" disabled>✕</button>
                </div>
            </div>

            <div style="margin-top:32px;display:flex;justify-content:flex-end;gap:12px">
                <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan PO Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
    let itemIndex = 1;
    function addPoItem() {
        const container = document.getElementById('poItemsContainer');
        const firstSelect = document.querySelector('select[name="items[0][product_variant_id]"]').innerHTML;
        
        const row = document.createElement('div');
        row.className = 'po-row';
        row.style.cssText = 'display:grid; grid-template-columns: 2fr 1fr 1fr 40px; gap:10px; margin-bottom:12px; align-items:end;';
        row.innerHTML = `
            <div class="form-group" style="margin:0">
                <select name="items[${itemIndex}][product_variant_id]" class="form-control" required>
                    ${firstSelect}
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" min="0" required>
            </div>
            <div class="form-group" style="margin:0">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" value="1" required>
            </div>
            <button type="button" class="btn btn-secondary btn-icon" onclick="this.parentElement.remove()">✕</button>
        `;
        container.appendChild(row);
        itemIndex++;
        
        const rows = document.querySelectorAll('.po-row');
        rows.forEach((r, idx) => {
            const btn = r.querySelector('button');
            if (btn) btn.disabled = rows.length === 1;
        });
    }
</script>
@endsection
