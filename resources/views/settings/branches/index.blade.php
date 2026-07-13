@extends('layouts.app')

@section('title', 'Manajemen Cabang')
@section('page-title', 'Cabang & Outlet')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Pengaturan</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Cabang & Outlet</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Daftar Cabang & Outlet</h1>
            <p class="page-header-subtitle">Kelola cabang toko baju Anda, pantau operasional, dan alokasikan stok terpisah.</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-primary" onclick="openBranchModal()">
                + Tambah Cabang
            </button>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Cabang</th>
                    <th>Nama Cabang</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $b)
                <tr>
                    <td style="font-weight: 700; color: var(--color-primary);">{{ $b->code }}</td>
                    <td style="font-weight: 600;">{{ $b->name }}</td>
                    <td>{{ $b->phone ?? '-' }}</td>
                    <td>{{ $b->address ?? '-' }}</td>
                    <td>
                        @if($b->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button class="btn btn-sm btn-ghost btn-icon" onclick='editBranch(@json($b))' title="Edit">
                                📝
                            </button>
                            <form action="{{ route('settings.branches.destroy', $b) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost btn-icon text-danger" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 40px 0; color: var(--text-muted);">Belum ada data cabang toko.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($branches->hasPages())
    <div style="padding:16px 24px; border-top:1px solid var(--border)">
        {{ $branches->links() }}
    </div>
    @endif
</div>

<!-- Modal -->
<div class="modal-overlay" id="branchModal" style="display:none;">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Cabang Toko</div>
            <button onclick="closeBranchModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="{{ route('settings.branches.store') }}" id="branchForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Cabang *</label>
                    <input type="text" name="code" id="b_code" class="form-control" placeholder="Contoh: JK01, BDG02" required style="text-transform: uppercase">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Cabang *</label>
                    <input type="text" name="name" id="b_name" class="form-control" placeholder="Contoh: FashionPOS Cabang Bandung" required>
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" id="b_phone" class="form-control" placeholder="Contoh: 0812xxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" id="b_address" class="form-control" rows="3" placeholder="Alamat lengkap outlet..."></textarea>
                </div>
                <div class="form-group" style="display:flex; align-items:center; margin-top:15px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                        <input type="checkbox" name="is_active" id="b_active" value="1" checked style="width:20px; height:20px;">
                        <span style="font-weight:600;">Aktifkan Cabang Ini</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeBranchModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBranchModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Cabang Toko';
        document.getElementById('branchForm').action = "{{ route('settings.branches.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('b_code').value = '';
        document.getElementById('b_name').value = '';
        document.getElementById('b_phone').value = '';
        document.getElementById('b_address').value = '';
        document.getElementById('b_active').checked = true;
        
        document.getElementById('b_code').disabled = false;
        
        document.getElementById('branchModal').style.display = 'flex';
    }

    function closeBranchModal() {
        document.getElementById('branchModal').style.display = 'none';
    }

    function editBranch(b) {
        document.getElementById('modalTitle').textContent = 'Edit Cabang Toko';
        document.getElementById('branchForm').action = `/settings/branches/${b.id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('b_code').value = b.code;
        document.getElementById('b_name').value = b.name;
        document.getElementById('b_phone').value = b.phone || '';
        document.getElementById('b_address').value = b.address || '';
        document.getElementById('b_active').checked = b.is_active;
        
        document.getElementById('b_code').disabled = true; // Code code cannot be modified once set

        document.getElementById('branchModal').style.display = 'flex';
    }
</script>
@endsection
