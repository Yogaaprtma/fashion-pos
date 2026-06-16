@extends('layouts.app')

@section('title', 'Daftar Supplier')
@section('page-title', 'Daftar Supplier')

@section('content')

{{-- Page Header Enhanced --}}
<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Pengadaan</span>
        <span class="sep">›</span>
        <span>Supplier</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box cyan">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1>Daftar Supplier</h1>
                <p class="subtitle">Kelola daftar pemasok dan vendor untuk kebutuhan stok barang.</p>
            </div>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-primary" onclick="document.getElementById('addSupplierModal').style.display='flex'">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Supplier
            </button>
        </div>
    </div>

    <div class="ph-stats-row">
        <div class="ph-stat">
            <div class="ph-stat-icon indigo">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ $suppliers->total() }}</div>
                <div class="ph-stat-lbl">Total Supplier</div>
            </div>
        </div>
        <div class="ph-stat">
            <div class="ph-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="ph-stat-val">{{ $suppliers->sum('purchase_orders_count') }}</div>
                <div class="ph-stat-lbl">Total Purchase Order</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar-enhanced">
    <form action="{{ route('purchase.suppliers.index') }}" method="GET" style="display:flex;gap:10px;width:100%;align-items:flex-end;">
        <div class="form-group" style="flex:1;max-width:420px;margin:0;">
            <label class="form-label">Cari Supplier</label>
            <div class="search-input">
                <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" class="form-control" placeholder="Nama supplier, kontak, email..." value="{{ request('search') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari
        </button>
        @if(request('search'))
        <a href="{{ route('purchase.suppliers.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="section-title-dot"></span>
            Daftar Supplier
        </div>
        <span class="badge badge-primary">{{ $suppliers->total() }} supplier</span>
    </div>
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
                    <td>
                        <div style="font-weight:700;color:#111827;">{{ $supplier->name }}</div>
                    </td>
                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td><span class="badge badge-primary">{{ $supplier->purchase_orders_count }} PO</span></td>
                    <td>
                        <form action="{{ route('purchase.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)" title="Hapus Supplier">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">🏭</div>
                            <div class="empty-state-title">Belum ada data supplier</div>
                            <div class="empty-state-desc">Tambahkan supplier pertama Anda untuk mulai membuat purchase order.</div>
                            <button class="btn btn-primary" onclick="document.getElementById('addSupplierModal').style.display='flex'">
                                + Tambah Supplier
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">{{ $suppliers->links() }}</div>
    @endif
</div>

{{-- Modal Tambah Supplier --}}
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
                    <input type="text" name="name" class="form-control" placeholder="CV. Maju Bersama" required>
                </div>
                <div class="grid grid-2" style="gap:16px">
                    <div class="form-group">
                        <label class="form-label">Nama Kontak (PIC)</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="Budi Santoso">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="supplier@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Jl. Contoh No. 1, Kota..."></textarea>
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
