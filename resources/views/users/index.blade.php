@extends('layouts.app')

@section('title', 'Manajemen User & Karyawan')
@section('page-title', 'Manajemen Karyawan')

@section('content')

<div class="card">
    <div class="card-header flex-between">
        <form action="{{ route('users.index') }}" method="GET" style="display:flex; gap:10px; width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama karyawan..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
        <button class="btn btn-primary" onclick="document.getElementById('addUserModal').style.display='flex'">+ Tambah Karyawan</button>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Karyawan</th>
                    <th>Email (Login)</th>
                    <th>Role / Posisi</th>
                    <th>PIN Kasir</th>
                    <th>Status</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr>
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td style="font-weight: 600;">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-primary">{{ $user->role?->name ?? 'Belum ada role' }}</span>
                    </td>
                    <td>
                        @if($user->hasRole('kasir'))
                            <span class="badge badge-success">Diatur</span>
                        @else
                            <span style="color:var(--text-muted); font-size:12px;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus/Nonaktifkan user ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary btn-icon" style="color:var(--color-danger)">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:30px;">Belum ada data karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

<!-- Modal Tambah Karyawan -->
<div class="modal-overlay" id="addUserModal" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Tambah Karyawan Baru</div>
            <button onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Untuk Login) *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="grid grid-2" style="gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role / Posisi *</label>
                        <select name="role" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" style="background:var(--bg-elevated); padding:12px; border-radius:8px;">
                    <label class="form-label" style="display:flex; justify-content:space-between;">
                        <span>PIN Kasir (4-6 Angka)</span>
                        <span class="badge badge-warning">Khusus Role Kasir</span>
                    </label>
                    <input type="number" name="pin" class="form-control" placeholder="Contoh: 1234">
                    <small style="color:var(--text-muted)">Digunakan untuk login cepat ke mesin POS.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>

@endsection
