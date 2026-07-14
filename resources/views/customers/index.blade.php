@extends('layouts.app')

@section('title', 'Manajemen Pelanggan (CRM)')

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Pelanggan & Member</h1>
        <p class="page-header-subtitle">Kelola data pelanggan, member loyal, dan pantau riwayat belanja.</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openCustomerModal()">
            + Tambah Pelanggan
        </button>
    </div>
</div>

<div class="card p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>No. Telepon</th>
                <th>Email</th>
                <th>Tgl. Lahir</th>
                <th>Total Belanja</th>
                <th>Poin</th>
                <th>Status Member</th>
                <th width="150" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $c)
            <tr>
                <td style="font-weight: 600;">{{ $c->name }}</td>
                <td>{{ $c->phone ?? '-' }}</td>
                <td>{{ $c->email ?? '-' }}</td>
                <td>{{ $c->birth_date ? $c->birth_date->format('d M Y') : '-' }}</td>
                <td>Rp {{ number_format($c->total_spent, 0, ',', '.') }}</td>
                <td><span class="badge badge-warning">{{ $c->points }} pts</span></td>
                <td>
                    @if($c->is_member)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Bukan Member</span>
                    @endif
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-secondary" onclick='editCustomer(@json($c))'>Edit</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding:40px;color:var(--text-muted)">Belum ada data pelanggan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    {{ $customers->links() }}
</div>

<!-- Modal -->
<div class="modal-overlay" id="customerModal" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Tambah Pelanggan</div>
            <button onclick="closeCustomerModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" id="customerForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="c_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon / WA</label>
                    <input type="text" name="phone" id="c_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="c_email" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="c_birth_date" class="form-control">
                </div>
                <div class="form-group" style="margin-top:15px">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                        <input type="hidden" name="is_member" value="0">
                        <input type="checkbox" name="is_member" id="c_member" value="1" style="width:20px;height:20px">
                        <span style="font-weight:600">Jadikan Member Loyal</span>
                    </label>
                    <small style="color:var(--text-muted);display:block;margin-top:4px">Member bisa mendapatkan diskon dan poin dari setiap pembelanjaan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeCustomerModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCustomerModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
    document.getElementById('customerForm').action = '{{ route('customers.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('customerForm').reset();
    document.getElementById('customerModal').style.display = 'flex';
}

function editCustomer(c) {
    document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
    document.getElementById('customerForm').action = '/customers/' + c.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('c_name').value = c.name;
    document.getElementById('c_phone').value = c.phone || '';
    document.getElementById('c_email').value = c.email || '';
    document.getElementById('c_birth_date').value = c.birth_date ? c.birth_date.substring(0, 10) : '';
    document.getElementById('c_member').checked = c.is_member;
    document.getElementById('customerModal').style.display = 'flex';
}

function closeCustomerModal() {
    document.getElementById('customerModal').style.display = 'none';
}
</script>
@endsection
