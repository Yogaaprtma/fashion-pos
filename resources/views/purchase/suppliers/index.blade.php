@extends('layouts.app')

@section('title', 'Daftar Supplier')
@section('page-title', 'Daftar Supplier')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Daftar Supplier</h1>
        <p class="page-header-subtitle">Kelola daftar pemasok/vendor untuk kebutuhan stok barang.</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="document.getElementById('addSupplierModal').style.display='flex'">
            + Tambah Supplier
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('purchase.suppliers.index') }}" method="GET" style="display:flex; gap:10px; width:100%;">
        <div class="search-input" style="flex:1; max-width:400px;">
            <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" class="form-control" placeholder="Cari nama supplier, kontak..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Kontak / PIC</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Total PO</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td style="font-weight:600">{{ $supplier->name }}</td>
                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td><span class="badge badge-secondary">{{ $supplier->purchase_orders_count }} PO</span></td>
                    <td>
                        <form action="{{ route('purchase.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Belum ada data supplier.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">{{ $suppliers->links() }}</div>
    @endif
</div>

<!-- Modal Tambah Supplier -->
<div class="modal-overlay" id="addSupplierModal" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Tambah Supplier Baru</div>
            <button onclick="document.getElementById('addSupplierModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form action="{{ route('purchase.suppliers.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Supplier / Perusahaan *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="grid grid-2" style="gap:16px">
                    <div class="form-group">
                        <label class="form-label">Nama Kontak (PIC)</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('addSupplierModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>

@endsection
