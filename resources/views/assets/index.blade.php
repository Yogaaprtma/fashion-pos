@extends('layouts.app')

@section('title', 'Aset & Inventaris')
@section('page-title', 'Manajemen Aset')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Manajemen Aset</h1>
        <p class="page-header-subtitle">Kelola dan pantau seluruh aset serta inventaris fisik perusahaan.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            + Tambah Aset
        </a>
    </div>
</div>

<div class="grid grid-3 mb-4">
    <div class="stat-card primary">
        <div class="stat-label">Total Nilai Aset</div>
        <div class="stat-value">{{ number_format($totalValue ?? 0, 0, ',', '.') }}</div>
        <div style="font-size:10px;color:var(--text-muted);font-family:monospace;margin-top:2px">Rp</div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Total Item Aset</div>
        <div class="stat-value">{{ $assets->total() }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px">item aktif</div>
    </div>
    <div class="stat-card warning" style="cursor:pointer" onclick="document.getElementById('addCategoryModal').style.display='flex'">
        <div class="stat-label">Kategori Aset</div>
        <div class="stat-value" style="font-size:24px">{{ count($categories) }} <span style="font-size:12px">kategori</span></div>
        <div style="margin-top:4px;color:var(--text-primary);font-size:11px;font-weight:600">Tambah/Kelola Kategori →</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form action="{{ route('assets.index') }}" method="GET" style="display:flex;gap:10px;width:100%">
        <div class="search-input" style="flex:1; max-width:400px;">
            <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" class="form-control" placeholder="Cari kode, nama aset..." value="{{ request('search') }}">
        </div>
        <select name="condition" class="form-control" style="width:200px">
            <option value="">Semua Kondisi</option>
            <option value="good" {{ request('condition') === 'good' ? 'selected' : '' }}>Baik</option>
            <option value="fair" {{ request('condition') === 'fair' ? 'selected' : '' }}>Cukup</option>
            <option value="poor" {{ request('condition') === 'poor' ? 'selected' : '' }}>Rusak</option>
        </select>
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode / Nama Aset</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Nilai Saat Ini</th>
                    <th>Kondisi</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary)">{{ $asset->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);font-family:monospace">{{ $asset->asset_code }}</div>
                    </td>
                    <td><span class="badge badge-secondary">{{ $asset->assetCategory?->name ?? '-' }}</span></td>
                    <td>{{ $asset->location ?? '-' }}</td>
                    <td class="currency" style="font-weight:600">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $condMap = [
                                'good' => ['label' => 'Baik', 'class' => 'badge-success'],
                                'fair' => ['label' => 'Cukup', 'class' => 'badge-warning'],
                                'poor' => ['label' => 'Rusak', 'class' => 'badge-danger'],
                                'disposed' => ['label' => 'Dihapus', 'class' => 'badge-secondary'],
                            ];
                            $c = $condMap[$asset->condition] ?? $condMap['good'];
                        @endphp
                        <span class="badge {{ $c['class'] }}">{{ $c['label'] }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-secondary btn-icon" title="Detail">👁️</a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Belum ada data aset terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assets->hasPages())
    <div class="card-footer">{{ $assets->links() }}</div>
    @endif
</div>

<!-- Modal Kategori -->
<div class="modal-overlay" id="addCategoryModal" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Kelola Kategori Aset</div>
            <button onclick="document.getElementById('addCategoryModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('assets.categories.store') }}" method="POST" style="display:flex;gap:10px;margin-bottom:20px">
                @csrf
                <input type="text" name="name" class="form-control" placeholder="Nama Kategori Baru" required>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>

            <table class="table" style="font-size:13px">
                <tbody>
                    @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->name }}</td>
                        <td width="60" style="text-align:right">
                            <form action="{{ route('assets.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary" style="color:var(--color-danger);padding:2px 8px">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
