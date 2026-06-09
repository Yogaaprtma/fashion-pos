@extends('layouts.app')

@section('title', 'Detail Purchase Order')
@section('page-title')
    <a href="{{ route('purchase.orders.index') }}" style="color:var(--text-muted);text-decoration:none;font-weight:400;margin-right:8px;">&larr; Kembali</a>
    PO #{{ $order->po_number }}
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body grid grid-4">
        <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Supplier</div>
            <div style="font-weight:600">{{ $order->supplier?->name }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Tanggal PO</div>
            <div style="font-weight:600">{{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Total Nilai PO</div>
            <div style="font-weight:600; color:var(--text-primary)">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </div>
        <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Status</div>
            @php
                $badgeMap = ['draft' => 'badge-secondary', 'sent' => 'badge-primary', 'partial' => 'badge-warning', 'received' => 'badge-success', 'cancelled' => 'badge-danger'];
            @endphp
            <span class="badge {{ $badgeMap[$order->status] ?? 'badge-secondary' }}">{{ strtoupper($order->status) }}</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header flex-between">
        <div class="card-title">Daftar Barang (Item)</div>
        @if(in_array($order->status, ['draft', 'sent', 'partial']))
            <button class="btn btn-success" onclick="document.getElementById('receiveModal').style.display='flex'">📥 Terima Barang (GRN)</button>
        @endif
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:right">Harga Beli</th>
                    <th style="text-align:center">Qty Pesan</th>
                    <th style="text-align:center">Qty Diterima</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $item->productVariant?->product?->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $item->productVariant?->variant_label }}</div>
                    </td>
                    <td style="text-align:right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align:center;font-weight:600">{{ $item->quantity_ordered }}</td>
                    <td style="text-align:center; color:{{ $item->quantity_received == $item->quantity_ordered ? 'var(--color-success)' : 'var(--color-warning)' }}">{{ $item->quantity_received }}</td>
                    <td style="text-align:right;font-weight:600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Terima Barang -->
@if(in_array($order->status, ['draft', 'sent', 'partial']))
<div class="modal-overlay" id="receiveModal" style="display:none">
    <div class="modal" style="max-width:600px">
        <div class="modal-header">
            <div class="modal-title">Penerimaan Barang (Good Received Note)</div>
            <button onclick="document.getElementById('receiveModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form action="{{ route('purchase.orders.receive', $order) }}" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                    Masukkan jumlah barang fisik yang diterima dari supplier. Stok sistem akan otomatis bertambah sesuai jumlah yang Anda masukkan di sini.
                </p>
                <div class="form-group">
                    <label class="form-label">No. Resi / Surat Jalan (Opsional)</label>
                    <input type="text" name="grn_number" class="form-control" placeholder="SJ-...">
                </div>
                
                <table class="table" style="margin-top:16px; border:1px solid var(--border)">
                    <thead style="background:var(--bg-elevated)">
                        <tr>
                            <th>Barang</th>
                            <th>Belum Diterima</th>
                            <th width="120">Terima (Qty)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        @php $pending = $item->quantity_ordered - $item->quantity_received; @endphp
                        <tr>
                            <td style="font-size:13px;">{{ $item->productVariant?->product?->name }} ({{ $item->productVariant?->variant_label }})</td>
                            <td style="text-align:center">{{ $pending }}</td>
                            <td>
                                <input type="number" name="received[{{ $item->id }}]" class="form-control" value="{{ $pending }}" min="0" max="{{ $pending }}" style="padding:4px; height:auto;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('receiveModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-success">Proses Penerimaan</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
