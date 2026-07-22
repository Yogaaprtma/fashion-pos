@extends('layouts.app')

@section('title', 'Biaya & Pengeluaran')
@section('page-title', 'Biaya & Pengeluaran')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Keuangan</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Biaya & Pengeluaran</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Pengeluaran Operasional Toko</h1>
            <p class="page-header-subtitle">Catat pengeluaran listrik, sewa ruko, gaji karyawan, dan kas kecil untuk menghitung laba bersih yang akurat.</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-primary" onclick="openExpenseModal()">
                + Tambah Pengeluaran
            </button>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="card" style="margin-bottom: 24px; padding: 16px;">
    <form action="{{ route('expenses.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(4, 1fr) auto; gap: 12px; align-items: end;">
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 11px;">Kategori Pengeluaran</label>
            <select name="expense_category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 11px;">Cabang / Outlet</label>
            <select name="branch_id" class="form-control">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 11px;">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" style="font-size: 11px;">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="padding: 9px 16px;">Filter</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary" style="padding: 9px 16px; display: flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>
</div>

<!-- Table List -->
<div class="card p-0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Cabang</th>
                    <th>Catatan / Keterangan</th>
                    <th>Nominal</th>
                    <th>Dicatat Oleh</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $e)
                <tr>
                    <td style="font-weight: 600;">{{ $e->expense_date->format('d M Y') }}</td>
                    <td><span class="badge badge-secondary" style="font-weight:600;">{{ $e->category->name }}</span></td>
                    <td style="font-weight:600; color:var(--color-primary);">{{ $e->branch ? $e->branch->name : 'Pusat / Global' }}</td>
                    <td>{{ $e->notes ?? '-' }}</td>
                    <td style="font-weight: 700; color: var(--color-danger);">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                    <td style="font-size: 12px;">{{ $e->user->name }}</td>
                    <td class="text-center">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button class="btn btn-sm btn-ghost btn-icon" onclick='editExpense(@json($e))' title="Edit">
                                📝
                            </button>
                            <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?')">
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
                    <td colspan="7" class="text-center" style="padding: 40px 0; color: var(--text-muted);">Belum ada catatan pengeluaran operasional yang sesuai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div style="padding:16px 24px; border-top:1px solid var(--border)">
        {{ $expenses->links() }}
    </div>
    @endif
</div>

<!-- Modal Form -->
<div class="modal-overlay" id="expenseModal" style="display:none;">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Catat Pengeluaran Operasional</div>
            <button onclick="closeExpenseModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="{{ route('expenses.store') }}" id="expenseForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tanggal Pengeluaran *</label>
                    <input type="date" name="expense_date" id="e_date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <select name="expense_category_id" id="e_category_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Cabang (Opsional)</label>
                    <select name="branch_id" id="e_branch_id" class="form-control">
                        <option value="">Pusat / Kantor Global</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal Pengeluaran (Rp) *</label>
                    <input type="number" name="amount" id="e_amount" class="form-control" placeholder="Contoh: 150000" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan / Detail *</label>
                    <textarea name="notes" id="e_notes" class="form-control" rows="3" placeholder="Contoh: Bayar tagihan listrik PLN bulan Juni 2026" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeExpenseModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExpenseModal() {
        document.getElementById('modalTitle').textContent = 'Catat Pengeluaran Operasional';
        document.getElementById('expenseForm').action = "{{ route('expenses.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('e_date').value = "{{ date('Y-m-d') }}";
        document.getElementById('e_category_id').value = '';
        document.getElementById('e_branch_id').value = '';
        document.getElementById('e_amount').value = '';
        document.getElementById('e_notes').value = '';

        document.getElementById('expenseModal').style.display = 'flex';
    }

    function closeExpenseModal() {
        document.getElementById('expenseModal').style.display = 'none';
    }

    function editExpense(e) {
        document.getElementById('modalTitle').textContent = 'Edit Catatan Pengeluaran';
        document.getElementById('expenseForm').action = `/expenses/${e.id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        // Format date object back to string Yyyy-Mm-Dd
        const expDate = e.expense_date.substring(0, 10);
        document.getElementById('e_date').value = expDate;
        document.getElementById('e_category_id').value = e.expense_category_id;
        document.getElementById('e_branch_id').value = e.branch_id || '';
        document.getElementById('e_amount').value = Math.round(e.amount);
        document.getElementById('e_notes').value = e.notes || '';

        document.getElementById('expenseModal').style.display = 'flex';
    }
</script>
@endsection
