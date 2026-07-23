@extends('layouts.app')

@section('title', 'Riwayat Poin - ' . $customer->name)
@section('page-title', 'Riwayat Poin Member')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">Riwayat Poin: {{ $customer->name }}</h1>
        <p class="page-header-subtitle">Rincian semua pergerakan poin reward pelanggan ini.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('loyalty.index') }}" class="btn btn-ghost">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>
</div>

{{-- Customer Summary Card --}}
<div class="card" style="margin-bottom:20px; background:linear-gradient(135deg, var(--color-primary) 0%, #9333EA 100%); color:white;">
    <div class="card-body" style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div style="width:64px; height:64px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:28px; flex-shrink:0;">
            {{ strtoupper(substr($customer->name, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:20px; font-weight:800;">{{ $customer->name }}</div>
            <div style="opacity:0.8; font-size:13px; margin-top:2px;">{{ $customer->phone ?? 'Tidak ada nomor' }}</div>
            <div style="margin-top:6px;">
                @php $tier = $customer->member_tier; @endphp
                <span style="background:rgba(255,255,255,0.25); padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                    {{ $tier === 'Gold' ? '🥇' : ($tier === 'Silver' ? '🥈' : '🥉') }} {{ $tier }} Member
                </span>
            </div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:40px; font-weight:900; line-height:1;">{{ number_format($customer->points ?? 0) }}</div>
            <div style="opacity:0.8; font-size:13px; margin-top:4px;">Poin Aktif</div>
            <div style="font-size:12px; opacity:0.7; margin-top:2px;">≈ Rp {{ number_format(($customer->points ?? 0) * 10, 0, ',', '.') }} diskon</div>
        </div>
    </div>
</div>

{{-- Point History Table --}}
<div class="card">
    <div class="card-header" style="padding:16px 20px; border-bottom:1px solid var(--border);">
        <h3 style="font-size:15px; font-weight:700; margin:0;">Riwayat Pergerakan Poin</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th width="180">Tanggal</th>
                    <th>Keterangan</th>
                    <th>Tipe</th>
                    <th>No. Invoice</th>
                    <th style="text-align:right;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $h)
                <tr>
                    <td style="color:var(--text-muted); font-size:13px;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $h->description ?? '-' }}</td>
                    <td>
                        @if($h->type === 'earn')
                            <span class="badge badge-success">Poin Masuk</span>
                        @elseif($h->type === 'redeem')
                            <span class="badge badge-warning">Ditukar</span>
                        @else
                            <span class="badge badge-secondary">Penyesuaian</span>
                        @endif
                    </td>
                    <td style="font-size:12px; font-family:monospace; color:var(--text-muted);">
                        {{ $h->transaction?->invoice_number ?? '-' }}
                    </td>
                    <td style="text-align:right; font-weight:800; font-size:15px; color:{{ $h->amount > 0 ? '#10B981' : '#EF4444' }};">
                        {{ $h->amount > 0 ? '+' : '' }}{{ number_format($h->amount) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-title">Belum ada riwayat poin</div>
                            <div class="empty-state-desc">Poin akan tercatat setiap kali pelanggan ini melakukan transaksi.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($histories->hasPages())
    <div class="card-footer">{{ $histories->links() }}</div>
    @endif
</div>
@endsection
