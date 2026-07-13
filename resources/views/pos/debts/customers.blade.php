@extends('layouts.app')

@section('title', 'Piutang Pelanggan (Kasbon)')
@section('page-title', 'Piutang & Kasbon Pelanggan')

@section('content')
<div class="page-header-enhanced" style="margin-bottom: 24px;">
    <div class="page-header-breadcrumb">
        <span class="breadcrumb-item">Pelanggan</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Piutang & Kasbon</span>
    </div>
    <div class="page-header-main">
        <div class="page-header-info">
            <h1 class="page-header-title">Daftar Kasbon Pelanggan</h1>
            <p class="page-header-subtitle">Lacak transaksi tempo pelanggan dan catat pelunasan piutang/kasbon.</p>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Transaksi</th>
                    <th>Sisa Piutang (Kasbon)</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td style="font-weight: 700; color: var(--color-primary);">{{ $t->invoice_number }}</td>
                    <td style="font-weight: 600;">{{ $t->customer ? $t->customer->name : 'Umum / Guest' }}</td>
                    <td>Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                    <td style="font-weight: 700; color: var(--color-danger);">Rp {{ number_format($t->remaining_debt, 0, ',', '.') }}</td>
                    <td>
                        @if($t->due_date)
                        <span style="{{ \Carbon\Carbon::parse($t->due_date)->isPast() ? 'color:var(--color-danger); font-weight:700;' : '' }}">
                            {{ \Carbon\Carbon::parse($t->due_date)->format('d M Y') }}
                        </span>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($t->payment_status === 'unpaid')
                        <span class="badge badge-danger">Belum Bayar</span>
                        @else
                        <span class="badge badge-warning">Bayar Sebagian</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" onclick='openPaymentModal(@json($t))'>
                            Bayar Cicilan
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px 0; color: var(--text-muted);">Belum ada piutang/kasbon pelanggan yang tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div style="padding:16px 24px; border-top:1px solid var(--border)">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

<!-- Modal Bayar Piutang -->
<div class="modal-overlay" id="payDebtModal" style="display:none;">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Catat Cicilan / Pelunasan Kasbon</div>
            <button onclick="closePaymentModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form method="POST" action="" id="payDebtForm">
            @csrf
            <div class="modal-body">
                <div style="margin-bottom:16px; padding:12px; background:var(--bg-elevated); border-radius:8px; border:1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                        <span style="color:var(--text-muted);">Invoice:</span>
                        <strong id="modalInvoiceNum" style="color:var(--text-primary);">#</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                        <span style="color:var(--text-muted);">Pelanggan:</span>
                        <strong id="modalCustomerName" style="color:var(--text-primary);">-</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:700; margin-top:8px; border-top:1px solid var(--border); padding-top:8px;">
                        <span style="color:var(--text-muted);">Total Kasbon:</span>
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
                    <input type="text" name="notes" class="form-control" placeholder="Contoh: Pembayaran cicilan ke-1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closePaymentModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-success">✓ Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(t) {
        document.getElementById('payDebtForm').action = `/customers/debts/${t.id}/pay`;
        document.getElementById('modalInvoiceNum').textContent = t.invoice_number;
        document.getElementById('modalCustomerName').textContent = t.customer ? t.customer.name : 'Umum / Guest';

        const remaining = parseFloat(t.remaining_debt);
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