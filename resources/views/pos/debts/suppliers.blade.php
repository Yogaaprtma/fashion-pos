@extends('layouts.app')

@section('title', 'Hutang ke Supplier')
@section('page-title', 'Hutang Supplier')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Pengadaan</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Hutang Supplier</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Daftar Hutang ke Supplier</h1>
            <p class="page-header-subtitle">Lacak pembelian barang tempo (termin) ke supplier dan catat pembayaran cicilan.</p>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Nama Supplier</th>
                    <th>Total Pembelian</th>
                    <th>Sisa Hutang</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td style="font-weight: 700; color: var(--color-primary);">{{ $o->po_number }}</td>
                    <td style="font-weight: 600;">{{ $o->supplier->name }}</td>
                    <td>Rp {{ number_format($o->total_amount, 0, ',', '.') }}</td>
                    <td style="font-weight: 700; color: var(--color-danger);">Rp {{ number_format($o->remaining_debt, 0, ',', '.') }}</td>
                    <td>
                        @if($o->due_date)
                        <span style="{{ \Carbon\Carbon::parse($o->due_date)->isPast() ? 'color:var(--color-danger); font-weight:700;' : '' }}">
                            {{ \Carbon\Carbon::parse($o->due_date)->format('d M Y') }}
                        </span>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($o->payment_status === 'unpaid')
                        <span class="badge badge-danger">Belum Bayar</span>
                        @else
                        <span class="badge badge-warning">Bayar Sebagian</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success" onclick='openPaymentModal(@json($o))'>
                            Bayar Hutang
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px 0; color: var(--text-muted);">Belum ada hutang supplier yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div style="padding:16px 24px; border-top:1px solid var(--border)">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Modal Bayar Hutang -->
<div class="modal-overlay" id="payDebtModal" style="display:none;">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Catat Pembayaran Hutang Supplier</div>
            <button onclick="closePaymentModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="" id="payDebtForm">
            @csrf
            <div class="modal-body">
                <div style="margin-bottom:16px; padding:12px; background:var(--bg-elevated); border-radius:8px; border:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                        <span style="color:var(--text-muted);">Purchase Order:</span>
                        <strong id="modalPoNum" style="color:var(--text-primary);">#</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                        <span style="color:var(--text-muted);">Supplier:</span>
                        <strong id="modalSupplierName" style="color:var(--text-primary);">-</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:700; margin-top:8px; border-top:1px solid var(--border); padding-top:8px;">
                        <span style="color:var(--text-muted);">Total Hutang:</span>
                        <span id="modalRemainingDebt" style="color:var(--color-danger);">Rp 0</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal Bayar (Rp) *</label>
                    <input type="number" name="amount" id="payAmount" class="form-control" placeholder="Masukkan jumlah pembayaran" required min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran *</label>
                    <select name="payment_method_id" class="form-control" required>
                        <option value="">-- Pilih Metode --</option>
                        @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Pembayaran</label>
                    <input type="text" name="notes" class="form-control" placeholder="Contoh: Pembayaran termin 1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closePaymentModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">✓ Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(o) {
        document.getElementById('payDebtForm').action = `/purchase/supplier-debts/${o.id}/pay`;
        document.getElementById('modalPoNum').textContent = o.po_number;
        document.getElementById('modalSupplierName').textContent = o.supplier.name;

        const remaining = parseFloat(o.remaining_debt);
        document.getElementById('modalRemainingDebt').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
        document.getElementById('payAmount').value = remaining;
        document.getElementById('payAmount').max = remaining;

        document.getElementById('payDebtModal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('payDebtModal').style.display = 'none';
    }
</script>
@endsection