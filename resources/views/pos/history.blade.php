@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi & Retur')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('pos.history') }}" method="GET" class="flex-between" style="width:100%">
            <div style="display:flex;gap:10px; flex-wrap:wrap;">
                <input type="text" name="search" class="form-control" placeholder="No Invoice..." value="{{ request('search') }}">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>Void / Batal</option>
                    <option value="partial_return" {{ request('status') == 'partial_return' ? 'selected' : '' }}>Ada Retur</option>
                </select>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice / Waktu</th>
                    <th>Kasir</th>
                    <th>Status</th>
                    <th>Total Item</th>
                    <th style="text-align:right">Total Nilai</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td>
                        <div style="font-weight:600;font-family:monospace">{{ $trx->invoice_number }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td>{{ $trx->cashierSession?->user?->name ?? 'Unknown' }}</td>
                    <td>
                        @php
                            $statusMap = [
                                'completed' => ['Selesai', 'badge-success'],
                                'voided' => ['Void/Batal', 'badge-danger'],
                                'partial_return' => ['Retur', 'badge-warning'],
                            ];
                            $c = $statusMap[$trx->status] ?? ['Unknown', 'badge-secondary'];
                        @endphp
                        <span class="badge {{ $c[1] }}">{{ $c[0] }}</span>
                    </td>
                    <td>{{ $trx->items->sum('quantity') }} pcs</td>
                    <td style="text-align:right; font-weight:600;">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('pos.transaction.show', $trx) }}" class="btn btn-sm btn-secondary btn-icon" title="Detail">👁️</a>
                            <a href="{{ route('pos.transaction.receipt', $trx) }}" target="_blank" class="btn btn-sm btn-secondary btn-icon" title="Struk Web">📄</a>
                            <a href="{{ route('pos.transaction.receipt-pdf', $trx) }}" target="_blank" class="btn btn-sm btn-secondary btn-icon" title="Cetak PDF">🖨️</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Belum ada riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
