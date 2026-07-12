@extends('layouts.app')

@section('title', 'Detail Mutasi Stok')
@section('page-title', 'Detail Mutasi Stok')

@section('content')
<div style="max-width:900px; margin:0 auto;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('inventory.transfers.index') }}" class="btn btn-secondary" style="font-size:12px;">← Kembali</a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px; background:#FEE2E2; color:#B91C1C; padding:12px; border-radius:8px; border:1px solid #FCA5A5;">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    <div class="card" style="margin-bottom:24px;">
        <div class="card-header flex-between">
            <div class="card-title">Mutasi Stok #{{ $transfer->transfer_number }}</div>
            <div>
                @if($transfer->status === 'pending')
                <span class="badge badge-warning" style="font-size:12px; padding:6px 12px;">Menunggu Persetujuan</span>
                @elseif($transfer->status === 'completed')
                <span class="badge badge-success" style="font-size:12px; padding:6px 12px;">Selesai (Stok Pindah)</span>
                @else
                <span class="badge badge-danger" style="font-size:12px; padding:6px 12px;">Dibatalkan</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; margin-bottom:24px; padding:16px; background:var(--bg-elevated); border-radius:8px;">
                <div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Cabang Asal</div>
                    <div style="font-size:15px; font-weight:700; margin-top:4px; color:var(--text-primary);">{{ $transfer->fromBranch->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Kode: {{ $transfer->fromBranch->code }}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Cabang Tujuan</div>
                    <div style="font-size:15px; font-weight:700; margin-top:4px; color:var(--text-primary);">{{ $transfer->toBranch->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Kode: {{ $transfer->toBranch->code }}</div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Pengaju</div>
                    <div style="font-size:15px; font-weight:700; margin-top:4px; color:var(--text-primary);">{{ $transfer->creator->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $transfer->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>

            @if($transfer->notes)
            <div style="margin-bottom:24px; padding:12px; border-left:4px solid var(--color-primary); background:rgba(79,70,229,0.05); font-size:13px; border-radius: 0 8px 8px 0;">
                <strong>Catatan:</strong> {{ $transfer->notes }}
            </div>
            @endif

            <div style="font-weight:700; margin-bottom:12px; color:var(--text-primary);">Daftar Item yang Dimutasi</div>
            <div class="table-responsive" style="border: 1px solid var(--border); border-radius:8px; overflow:hidden;">
                <table class="table" style="margin:0;">
                    <thead style="background:var(--bg-elevated)">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Ukuran</th>
                            <th>Warna</th>
                            <th width="120" class="text-center">Jumlah (Qty)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfer->items as $item)
                        <tr>
                            <td style="font-weight:600;">{{ $item->productVariant->product->name }}</td>
                            <td><span class="badge badge-secondary">{{ $item->productVariant->size }}</span></td>
                            <td><span class="badge badge-secondary">{{ $item->productVariant->color }}</span></td>
                            <td class="text-center" style="font-weight:700; font-size:15px;">{{ $item->quantity }} Pcs</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($transfer->status === 'pending' && auth()->user()->hasAnyRole(['admin', 'manajemen', 'supervisor']))
            <hr style="margin:24px 0; border-color:var(--border);">
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <form action="{{ route('inventory.transfers.update', $transfer) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan mutasi ini?')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn btn-danger">✕ Batalkan Mutasi</button>
                </form>

                <form action="{{ route('inventory.transfers.update', $transfer) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui mutasi ini? Stok cabang asal akan langsung dipotong dan dipindahkan ke cabang tujuan.')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-success">✓ Setujui & Pindahkan Stok</button>
                </form>
            </div>
            @endif

            @if($transfer->status === 'completed')
            <hr style="margin:24px 0; border-color:var(--border);">
            <div style="font-size:12px; color:var(--text-muted); text-align:right;">
                Disetujui oleh <strong>{{ $transfer->approver->name }}</strong> pada {{ $transfer->updated_at->format('d M Y H:i') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection