@extends('layouts.app')

@section('title', 'Laporan Shift Kasir')

@section('content')

<div class="card" style="max-width:600px; margin: 20px auto;">
    <div class="card-header flex-between" style="border-bottom: 2px dashed var(--border);">
        <div class="card-title">Ringkasan Shift ({{ $session->user?->name }})</div>
        <span class="badge badge-secondary">{{ $session->opened_at->format('d M Y') }}</span>
    </div>
    
    <div class="card-body">
        <div style="text-align:center; margin-bottom:24px;">
            <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Durasi Shift</div>
            <div style="font-weight:700;">{{ $session->opened_at->format('H:i') }} &rarr; {{ $session->closed_at ? $session->closed_at->format('H:i') : 'Sekarang' }}</div>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:24px; font-size:14px;">
            <tr>
                <td style="padding:8px 0; color:var(--text-muted);">Saldo Awal (Modal)</td>
                <td style="padding:8px 0; text-align:right; font-weight:600;">Rp {{ number_format($session->opening_balance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:var(--text-muted);">Pemasukan Kas/Tunai</td>
                <td style="padding:8px 0; text-align:right; font-weight:600; color:var(--color-success)">
                    + Rp {{ number_format($session->expected_balance - $session->opening_balance, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="border-top: 1px solid var(--border);">
                <td style="padding:12px 0; font-weight:700;">Saldo Seharusnya di Laci</td>
                <td style="padding:12px 0; text-align:right; font-weight:700;">Rp {{ number_format($session->expected_balance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:var(--text-muted);">Saldo Fisik Laci (Dihitung)</td>
                <td style="padding:8px 0; text-align:right; font-weight:600;">Rp {{ number_format($session->closing_balance, 0, ',', '.') }}</td>
            </tr>
            
            <tr style="border-top: 1px solid var(--border); background: {{ $session->difference < 0 ? 'rgba(244,63,94,0.1)' : ($session->difference > 0 ? 'rgba(16,185,129,0.1)' : 'transparent') }}">
                <td style="padding:12px; font-weight:700;">Selisih</td>
                <td style="padding:12px; text-align:right; font-weight:700; color:{{ $session->difference < 0 ? 'var(--color-danger)' : ($session->difference > 0 ? 'var(--color-success)' : 'inherit') }}">
                    {{ $session->difference > 0 ? '+' : '' }} Rp {{ number_format($session->difference, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        
        @if($session->notes)
        <div style="background:var(--bg-elevated); padding:12px; border-radius:8px; font-size:13px; color:var(--text-muted);">
            <strong>Catatan Kasir:</strong><br>{{ $session->notes }}
        </div>
        @endif

        <div style="margin-top:24px; display:flex; gap:12px;">
            <button class="btn btn-secondary btn-block" onclick="window.print()">🖨️ Cetak Laporan</button>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block" style="text-align:center;">Kembali ke Dashboard</a>
        </div>
    </div>
</div>

@endsection
