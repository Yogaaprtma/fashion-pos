@extends('layouts.app')

@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order (PO)')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <form action="{{ route('purchase.orders.index') }}" method="GET" class="flex-between" style="width:100%">
            <div style="display:flex;gap:10px">
                <select name="status" class="form-control" style="width:200px" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial (Diterima Sebagian)</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received (Lengkap)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary">+ Buat PO Baru</a>
        </form>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>No. PO / Tanggal</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th>Total Item</th>
                    <th>Total Nilai</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary)">{{ $order->po_number }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $order->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600">{{ $order->supplier?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $order->supplier?->contact_person }}</div>
                    </td>
                    <td>
                        @php
                            $badgeMap = [
                                'draft' => 'badge-secondary',
                                'sent' => 'badge-primary',
                                'partial' => 'badge-warning',
                                'received' => 'badge-success',
                                'cancelled' => 'badge-danger',
                            ];
                        @endphp
                        <span class="badge {{ $badgeMap[$order->status] ?? 'badge-secondary' }}">{{ strtoupper($order->status) }}</span>
                    </td>
                    <td>{{ $order->items->sum('quantity_ordered') }} pcs</td>
                    <td class="currency">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('purchase.orders.show', $order) }}" class="btn btn-sm btn-secondary">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Belum ada data Purchase Order.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">{{ $orders->links() }}</div>
    @endif
</div>

@endsection
