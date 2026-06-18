@extends('layouts.app')

@section('title', 'Detail Retur ' . $return->return_number)
@section('page-title', 'Detail Retur')

@section('content')

@if(session('success'))
    <div class="alert alert-success mb-4" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 18px;border-radius:10px;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert mb-4" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:14px 18px;border-radius:10px;">
        ❌ {{ session('error') }}
    </div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

    <!-- Left: Detail -->
    <div>
        <div class="card mb-4">
            <div class="card-header">
                <div>
                    <div style="font-weight:700;font-size:18px;font-family:monospace;">{{ $return->return_number }}</div>
                    <div style="font-size:13px;color:var(--text-muted);">
                        Dari Invoice: <a href="{{ route('pos.transaction.show', $return->transaction_id) }}"
                            style="color:var(--color-primary);">{{ $return->transaction?->invoice_number }}</a>
                    </div>
                </div>
                @if($return->status === 'pending')
                    <span class="badge badge-warning" style="font-size:13px;padding:6px 14px;">⏳ Menunggu Persetujuan</span>
                @elseif($return->status === 'approved')
                    <span class="badge badge-success" style="font-size:13px;padding:6px 14px;">✅ Disetujui</span>
                @else
                    <span class="badge badge-danger" style="font-size:13px;padding:6px 14px;">❌ Ditolak</span>
                @endif
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Diminta Oleh</div>
                        <div style="font-weight:600;">{{ $return->requestedBy?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Tanggal Retur</div>
                        <div style="font-weight:600;">{{ $return->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @if($return->approved_by)
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Diproses Oleh</div>
                        <div style="font-weight:600;">{{ $return->approvedBy?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Tanggal Proses</div>
                        <div style="font-weight:600;">{{ $return->approved_at?->format('d M Y, H:i') }}</div>
                    </div>
                    @endif
                </div>

                <div style="background:var(--bg-secondary);border-radius:8px;padding:14px;margin-bottom:16px;">
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;">Alasan Retur</div>
                    <div style="font-size:14px;color:var(--text-primary);">{{ $return->reason }}</div>
                </div>
            </div>
        </div>

        <!-- Item yang Diretur -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:15px;font-weight:600;">📦 Item yang Diretur</h3>
                <span style="font-size:13px;color:var(--text-muted);">Total Refund:
                    <strong style="color:var(--color-danger);">Rp {{ number_format($return->total_refund, 0, ',', '.') }}</strong>
                </span>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Refund per Item</th>
                            <th style="text-align:right;">Subtotal Refund</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $item)
                        <tr>
                            <td>{{ $item->productVariant?->product?->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $item->productVariant?->size }} / {{ $item->productVariant?->color }}
                                </span>
                            </td>
                            <td style="text-align:center;font-weight:700;">{{ $item->quantity }}</td>
                            <td style="text-align:right;">Rp {{ number_format($item->refund_amount / $item->quantity, 0, ',', '.') }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--color-danger);">Rp {{ number_format($item->refund_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Action Panel -->
    <div>
        @if($return->status === 'pending' && auth()->user()->canAccess('pos.return'))
        <div class="card">
            <div class="card-header">
                <h3 style="margin:0;font-size:15px;font-weight:600;">⚡ Tindakan</h3>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">

                <form method="POST" action="{{ route('returns.approve', $return) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width:100%;"
                            onclick="return confirm('Setujui retur ini? Stok akan dikembalikan otomatis.')">
                        ✅ Setujui Retur & Kembalikan Stok
                    </button>
                </form>

                <div style="position:relative;text-align:center;">
                    <div style="border-top:1px solid var(--border);margin:8px 0;"></div>
                    <span style="background:var(--bg-card);padding:0 10px;font-size:12px;color:var(--text-muted);position:relative;top:-10px;">ATAU</span>
                </div>

                <form method="POST" action="{{ route('returns.reject', $return) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:10px;">
                        <textarea name="reason" class="form-control" rows="3"
                                  placeholder="Tuliskan alasan penolakan retur..." required style="resize:vertical;"></textarea>
                        @error('reason') <div style="color:var(--color-danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-secondary" style="width:100%;color:var(--color-danger);"
                            onclick="return confirm('Tolak retur ini?')">
                        ❌ Tolak Retur
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="card mt-4" style="margin-top:16px;">
            <div class="card-body">
                <a href="{{ route('returns.index') }}" class="btn btn-secondary" style="width:100%;">← Kembali ke Daftar Retur</a>
            </div>
        </div>
    </div>
</div>

@endsection
