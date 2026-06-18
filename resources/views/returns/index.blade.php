@extends('layouts.app')

@section('title', 'Manajemen Retur')
@section('page-title', 'Manajemen Retur Barang')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <div style="display:flex;gap:10px;align-items:center;">
            <form method="GET" action="{{ route('returns.index') }}" style="display:flex;gap:10px;">
                <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>⏳ Menunggu</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>✅ Disetujui</option>
                    <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>❌ Ditolak</option>
                </select>
            </form>
        </div>
        <div style="font-size:13px;color:var(--text-muted);">
            Total: <strong>{{ $returns->total() }}</strong> retur
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Retur</th>
                    <th>No. Invoice Asal</th>
                    <th>Diminta Oleh</th>
                    <th>Tanggal</th>
                    <th style="text-align:right;">Total Refund</th>
                    <th style="text-align:center;">Status</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td>
                        <div style="font-weight:600;font-family:monospace;">{{ $return->return_number }}</div>
                    </td>
                    <td>
                        <a href="{{ route('pos.transaction.show', $return->transaction_id) }}"
                           style="color:var(--color-primary);font-family:monospace;">
                            {{ $return->transaction?->invoice_number ?? '-' }}
                        </a>
                    </td>
                    <td>{{ $return->requestedBy?->name ?? '-' }}</td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $return->created_at->format('d M Y, H:i') }}</td>
                    <td style="text-align:right;font-weight:600;">Rp {{ number_format($return->total_refund, 0, ',', '.') }}</td>
                    <td style="text-align:center;">
                        @if($return->status === 'pending')
                            <span class="badge badge-warning">⏳ Menunggu</span>
                        @elseif($return->status === 'approved')
                            <span class="badge badge-success">✅ Disetujui</span>
                        @else
                            <span class="badge badge-danger">❌ Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('returns.show', $return) }}" class="btn btn-sm btn-secondary">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <div style="font-size:36px;margin-bottom:10px;">📋</div>
                        Belum ada data retur.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
    <div class="card-footer">{{ $returns->links() }}</div>
    @endif
</div>

@endsection
