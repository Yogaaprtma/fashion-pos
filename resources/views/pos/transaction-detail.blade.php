@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title')
    <a href="{{ route('pos.history') }}" style="color:var(--text-muted);text-decoration:none;font-weight:400;margin-right:8px;">&larr; Kembali</a>
    INV: {{ $transaction->invoice_number }}
@endsection

@section('content')
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    <div class="card">
        <div class="card-header flex-between">
            <div class="card-title">Barang Belanjaan</div>
            <a href="{{ route('pos.transaction.receipt', $transaction) }}" target="_blank" class="btn btn-sm btn-primary">📄 Lihat Struk</a>
        </div>
        <div class="card-body" style="padding:0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Harga</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $item->product_name }}</div>
                            <div style="font-size:11px;color:var(--text-muted)">{{ $item->variant_info }}</div>
                        </td>
                        <td style="text-align:center">{{ $item->quantity }}</td>
                        <td style="text-align:right">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            @if($item->discount_amount > 0)
                                <br><small style="color:var(--color-danger)">- Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td style="text-align:right; font-weight:600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:24px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Ringkasan Pembayaran</div>
            </div>
            <div class="card-body">
                <div class="flex-between" style="margin-bottom:8px">
                    <span style="color:var(--text-muted)">Subtotal</span>
                    <span style="font-weight:600">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex-between" style="margin-bottom:8px; color:var(--color-danger)">
                    <span>Diskon Total</span>
                    <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex-between" style="margin-bottom:8px">
                    <span style="color:var(--text-muted)">PPN</span>
                    <span style="font-weight:600">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
                <hr style="margin:12px 0; border-color:var(--border)">
                <div class="flex-between" style="margin-bottom:16px; font-size:18px; font-weight:800;">
                    <span>GRAND TOTAL</span>
                    <span style="color:var(--color-primary)">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
                
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px; font-weight:600; text-transform:uppercase;">Metode Bayar</div>
                @foreach($transaction->payments as $pay)
                    <div class="flex-between" style="margin-bottom:4px; font-size:13px;">
                        <span>{{ $pay->paymentMethod?->name ?? 'Unknown' }}</span>
                        <span>Rp {{ number_format($pay->amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex-between" style="margin-top:8px; padding-top:8px; border-top:1px dashed var(--border);">
                    <span style="color:var(--text-muted)">Kembalian</span>
                    <span style="font-weight:600">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Retur & Void -->
        @if($transaction->status == 'completed' || $transaction->status == 'partial_return')
        <div class="card">
            <div class="card-body">
                <button class="btn btn-warning btn-block mb-3" onclick="document.getElementById('returnModal').style.display='flex'">🔄 Retur Barang</button>
                <form action="{{ route('pos.transaction.void', $transaction) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan/void seluruh transaksi ini?')">
                    @csrf
                    <!-- Inject input API directly since this uses form instead of API in demo -->
                    <input type="hidden" name="reason" value="Kesalahan kasir (Auto Void)">
                    <button type="submit" class="btn btn-secondary btn-block" style="color:var(--color-danger); border-color:var(--color-danger)">❌ Void (Batalkan Transaksi)</button>
                </form>
            </div>
        </div>
        @elseif($transaction->status == 'voided')
        <div class="card" style="background:rgba(244,63,94,0.1); border-color:rgba(244,63,94,0.2);">
            <div class="card-body">
                <div style="color:var(--color-danger); font-weight:600; margin-bottom:4px;">❌ Transaksi Telah Dibatalkan (Void)</div>
                <div style="font-size:12px; color:var(--text-muted)">Alasan: {{ $transaction->void_reason ?? 'Tidak ada catatan' }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Retur -->
<div class="modal-overlay" id="returnModal" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Retur Barang</div>
            <button onclick="document.getElementById('returnModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form action="{{ route('pos.transaction.return', $transaction) }}" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                    Isi jumlah barang yang ingin diretur. Stok barang akan otomatis dikembalikan ke gudang/toko.
                </p>
                <div class="form-group">
                    <label class="form-label">Alasan Retur *</label>
                    <select name="reason" class="form-control" required>
                        <option value="Salah Ukuran/Warna">Salah Ukuran / Warna</option>
                        <option value="Cacat/Rusak">Barang Cacat / Rusak</option>
                        <option value="Lainnya">Alasan Lainnya</option>
                    </select>
                </div>
                
                <table class="table" style="margin-top:16px; border:1px solid var(--border)">
                    <thead style="background:var(--bg-elevated)">
                        <tr>
                            <th>Barang</th>
                            <th style="text-align:center">Beli</th>
                            <th width="100">Retur (Qty)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                        <tr>
                            <td style="font-size:13px;">{{ $item->product_name }} <br><small style="color:var(--text-muted)">{{ $item->variant_info }}</small></td>
                            <td style="text-align:center">{{ $item->quantity }}</td>
                            <td>
                                <input type="number" name="return_items[{{ $item->id }}]" class="form-control" value="0" min="0" max="{{ $item->quantity }}" style="padding:4px; height:auto; text-align:center;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('returnModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-warning">Proses Retur</button>
            </div>
        </form>
    </div>
</div>

@endsection
