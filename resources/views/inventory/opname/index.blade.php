@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname')

@section('content')

<div class="card mb-4">
    <div class="card-header flex-between">
        <div class="card-title">Riwayat Opname</div>
        <button class="btn btn-primary" onclick="document.getElementById('addOpnameModal').style.display='flex'">+ Mulai Opname Baru</button>
    </div>
    <div class="card-body" style="padding:0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode / Tanggal</th>
                    <th>Penanggung Jawab</th>
                    <th>Status</th>
                    <th>Total Item</th>
                    <th>Total Selisih (Rp)</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opnames as $opname)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $opname->opname_number }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $opname->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td>{{ $opname->createdBy->name ?? '-' }}</td>
                    <td>
                        @if($opname->status == 'draft')
                            <span class="badge badge-warning">DRAFT</span>
                        @elseif($opname->status == 'completed')
                            <span class="badge badge-success">SELESAI</span>
                        @endif
                    </td>
                    <td>{{ $opname->items->count() }} jenis barang</td>
                    @php
                        // Hitung total selisih nilai (Qty Diff * Buy Price)
                        $totalDiffValue = 0;
                        foreach($opname->items as $item) {
                            $totalDiffValue += ($item->actual_qty - $item->system_qty) * ($item->productVariant->effective_buy_price ?? 0);
                        }
                    @endphp
                    <td>
                        @if($totalDiffValue < 0)
                            <span style="color:var(--color-danger);font-weight:600">Rp {{ number_format($totalDiffValue, 0, ',', '.') }}</span>
                        @elseif($totalDiffValue > 0)
                            <span style="color:var(--color-success);font-weight:600">+ Rp {{ number_format($totalDiffValue, 0, ',', '.') }}</span>
                        @else
                            <span style="color:var(--text-muted)">Rp 0</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('inventory.opname.show', $opname) }}" class="btn btn-sm btn-secondary">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:30px">Belum ada riwayat stock opname.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($opnames->hasPages())
    <div class="card-footer">{{ $opnames->links() }}</div>
    @endif
</div>

<!-- Modal Mulai Opname -->
<div class="modal-overlay" id="addOpnameModal" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-header">
            <div class="modal-title">Mulai Stock Opname Baru</div>
            <button onclick="document.getElementById('addOpnameModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
        </div>
        <form action="{{ route('inventory.opname.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                    Sistem akan membuat draft lembar kerja opname baru. Anda bisa memfilter kategori tertentu atau melakukan opname untuk seluruh toko.
                </p>
                <div class="form-group">
                    <label class="form-label">Kategori (Opsional)</label>
                    <select name="category_id" class="form-control">
                        <option value="">Semua Kategori (Full Opname)</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Opname bulanan Mei..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('addOpnameModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Lembar Kerja</button>
            </div>
        </form>
    </div>
</div>

@endsection
