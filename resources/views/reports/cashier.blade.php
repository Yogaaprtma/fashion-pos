@extends('layouts.app')

@section('title', 'Laporan per Kasir')
@section('page-title', 'Laporan Performa Kasir')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <form method="GET" action="{{ route('reports.cashier') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Filter Kasir</label>
                <select name="user_id" class="form-control" style="min-width:180px;">
                    <option value="">Semua Kasir</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
        </form>
    </div>
</div>

@if($kasirData->count() > 0)

<!-- Ringkasan per Kasir -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
    @foreach($kasirData as $userId => $data)
    <div class="card">
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <img src="{{ $data['user']?->avatar_url }}" alt="{{ $data['user']?->name }}"
                     style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                <div>
                    <div style="font-weight:700;color:var(--text-primary);">{{ $data['user']?->name }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $data['sessions']->count() }} sesi kerja</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div style="background:var(--bg-secondary);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">Total Transaksi</div>
                    <div style="font-size:22px;font-weight:700;color:var(--color-primary);">{{ number_format($data['total_trx']) }}</div>
                </div>
                <div style="background:var(--bg-secondary);border-radius:8px;padding:10px;text-align:center;">
                    <div style="font-size:11px;color:var(--text-muted);">Rata-rata/Trx</div>
                    <div style="font-size:16px;font-weight:700;color:var(--color-success);">Rp {{ number_format($data['avg_per_trx'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div style="margin-top:10px;padding:10px;background:rgba(37,99,235,.05);border-radius:8px;border:1px solid rgba(37,99,235,.1);">
                <div style="font-size:11px;color:var(--text-muted);">Total Omzet</div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary);">Rp {{ number_format($data['total_sales'], 0, ',', '.') }}</div>
            </div>
            @if(abs($data['total_diff']) > 0)
            <div style="margin-top:8px;padding:8px 10px;background:rgba(239,68,68,.05);border-radius:8px;border:1px solid rgba(239,68,68,.15);">
                <div style="font-size:11px;color:var(--text-muted);">Total Selisih Kasir</div>
                <div style="font-size:14px;font-weight:600;color:{{ $data['total_diff'] >= 0 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                    {{ $data['total_diff'] >= 0 ? '+' : '' }}Rp {{ number_format($data['total_diff'], 0, ',', '.') }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Detail Sesi -->
<div class="card">
    <div class="card-header">
        <h3 style="margin:0;font-size:15px;font-weight:600;">📋 Detail Setiap Sesi</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Kasir</th>
                    <th>Buka Sesi</th>
                    <th>Tutup Sesi</th>
                    <th style="text-align:center;">Total Trx</th>
                    <th style="text-align:right;">Total Penjualan</th>
                    <th style="text-align:right;">Selisih</th>
                    <th style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->user?->name }}</td>
                    <td style="font-size:13px;">{{ $session->opened_at->format('d M Y, H:i') }}</td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $session->closed_at?->format('d M Y, H:i') ?? '-' }}</td>
                    <td style="text-align:center;font-weight:700;">{{ $session->total_transactions }}</td>
                    <td style="text-align:right;">Rp {{ number_format($session->total_sales, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:600;color:{{ ($session->difference ?? 0) >= 0 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                        @if($session->difference !== null)
                            {{ $session->difference >= 0 ? '+' : '' }}Rp {{ number_format($session->difference, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($session->status === 'open')
                            <span class="badge badge-success">🟢 Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tutup</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px;color:var(--text-muted);">
        <div style="font-size:48px;margin-bottom:16px;">👤</div>
        <div style="font-size:16px;font-weight:600;">Belum ada data sesi kasir</div>
        <div style="font-size:13px;margin-top:6px;">pada periode {{ $dateFrom }} s/d {{ $dateTo }}</div>
    </div>
</div>
@endif

@endsection
