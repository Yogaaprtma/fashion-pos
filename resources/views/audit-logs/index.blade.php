@extends('layouts.app')

@section('title', 'Log Audit Sistem')
@section('page-title', 'Log Aktivitas Sistem (Audit Log)')

@section('content')
<div class="card mb-4" style="background:var(--bg-elevated); border:none;">
    <div class="card-body">
        <p style="margin:0; font-size:13px; color:var(--text-muted)">
            Menampilkan catatan aktivitas user dan sistem secara berurutan. Sangat berguna untuk melacak perubahan data, aktivitas login, kasir, dan inventori untuk keperluan audit keamanan.
        </p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="table" style="font-size:13px;">
            <thead>
                <tr>
                    <th width="160">Waktu</th>
                    <th>User / Aktor</th>
                    <th>Aktivitas</th>
                    <th>Modul / Fitur</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="color:var(--text-muted)">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td style="font-weight:600">{{ $log->user?->name ?? 'Sistem' }}</td>
                    <td>
                        <div style="font-weight:500; margin-bottom:4px;">{{ $log->action }}</div>
                        @if($log->description)
                            <div style="font-size:11px; color:var(--text-muted)">{{ $log->description }}</div>
                        @endif
                    </td>
                    <td><span class="badge badge-secondary">{{ $log->module ?? 'General' }}</span></td>
                    <td style="font-family:monospace; color:var(--text-muted)">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:30px">Belum ada catatan aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
