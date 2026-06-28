@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori Produk')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Kategori Produk</h1>
        <p class="page-header-subtitle">Kelola kategori dan sub-kategori untuk mengorganisir produk Anda.</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 320px; gap: 20px;">
    <!-- Kategori List -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Kategori</div>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Sub Kategori</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <div style="font-weight:600;display:flex;align-items:center;gap:8px">
                                <span style="font-size:20px">{{ $category->icon ?? '📁' }}</span>
                                {{ $category->name }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $category->children_count }} sub</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('inventory.categories.destroy', $category) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @foreach($category->children as $child)
                    <tr style="background:var(--bg-elevated)">
                        <td style="padding-left:40px">
                            <div style="display:flex;align-items:center;gap:8px">
                                <span style="color:var(--text-muted)">└</span>
                                <span>{{ $child->icon ?? '📄' }}</span>
                                {{ $child->name }}
                            </div>
                        </td>
                        <td>-</td>
                        <td>
                            @if($child->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('inventory.categories.destroy', $child) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus sub kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:30px">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tambah Kategori -->
    <div class="card" style="align-self: start;">
        <div class="card-header">
            <div class="card-title">Tambah Kategori</div>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Kategori *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Mis: Atasan, Celana...">
                </div>
                <div class="form-group">
                    <label class="form-label">Induk Kategori</label>
                    <select name="parent_id" class="form-control">
                        <option value="">(Sebagai Kategori Utama)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ikon (Emoji)</label>
                    <input type="text" name="icon" class="form-control" placeholder="👕, 👖, dll..." maxlength="10">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Simpan</button>
            </form>
        </div>
    </div>
</div>

@endsection
