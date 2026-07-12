@extends('layouts.app')

@section('title', 'Ajukan Mutasi Stok')
@section('page-title', 'Ajukan Mutasi Stok')

@section('content')
<div class="card" style="max-width:900px; margin:0 auto;">
    <div class="card-header">
        <div class="card-title">Formulir Pengajuan Mutasi Stok</div>
    </div>
    <div class="card-body">
        <form action="{{ route('inventory.transfers.store') }}" method="POST" id="transferForm">
            @csrf

            <div class="grid grid-2" style="gap:16px; margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Cabang Asal *</label>
                    <select name="from_branch_id" id="fromBranch" class="form-control" required>
                        <option value="">-- Pilih Cabang Asal --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Cabang Tujuan *</label>
                    <select name="to_branch_id" id="toBranch" class="form-control" required>
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr style="margin:24px 0; border-color:var(--border);">

            <div class="flex-between" style="margin-bottom:16px;">
                <div style="font-weight:700; color:var(--text-primary);">Daftar Barang Mutasi</div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">+ Tambah Barang</button>
            </div>

            <div id="transferItemsContainer">
                <!-- Rows appended by JS -->
            </div>

            <div class="form-group" style="margin-top:20px;">
                <label class="form-label">Catatan Mutasi (Opsional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan alasan mutasi atau info tambahan pengiriman..."></textarea>
            </div>

            <div style="margin-top:32px; display:flex; justify-content:flex-end; gap:12px">
                <a href="{{ route('inventory.transfers.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Ajukan Mutasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    let itemIndex = 0;
    const variants = @json($variants);

    function addItemRow() {
        const container = document.getElementById('transferItemsContainer');
        const row = document.createElement('div');
        row.className = 'transfer-item-row';
        row.style.cssText = 'display:grid; grid-template-columns: 2fr 1fr 40px; gap:12px; margin-bottom:12px; align-items:end;';
        
        let options = '<option value="">-- Pilih Barang --</option>';
        variants.forEach(v => {
            const label = `${v.product.name} - ${v.size} / ${v.color} (Global Stok: ${v.stock_qty})`;
            options += `<option value="${v.id}">${label}</option>`;
        });

        row.innerHTML = `
            <div class="form-group" style="margin:0;">
                <label class="form-label">Nama Barang</label>
                <select name="items[${itemIndex}][product_variant_id]" class="form-control" required>
                    ${options}
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Jumlah Mutasi (Qty)</label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" value="1" min="1" required>
            </div>
            <button type="button" class="btn btn-danger btn-icon" onclick="this.parentElement.remove()" style="height:36px; display:flex; align-items:center; justify-content:center;">✕</button>
        `;
        
        container.appendChild(row);
        itemIndex++;
    }

    // Append one default row
    document.addEventListener('DOMContentLoaded', () => {
        addItemRow();
    });

    document.getElementById('transferForm').addEventListener('submit', e => {
        const from = document.getElementById('fromBranch').value;
        const to = document.getElementById('toBranch').value;
        const rows = document.querySelectorAll('.transfer-item-row');

        if(from === to) {
            e.preventDefault();
            alert('Cabang asal dan cabang tujuan tidak boleh sama!');
            return;
        }

        if(rows.length === 0) {
            e.preventDefault();
            alert('Harap pilih minimal 1 barang untuk dimutasi.');
            return;
        }
    });
</script>
@endsection
