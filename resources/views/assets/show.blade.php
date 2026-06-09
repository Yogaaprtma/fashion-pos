@extends('layouts.app')

@section('title', 'Detail Aset')
@section('page-title')
    <a href="{{ route('assets.index') }}" style="color:var(--text-muted);text-decoration:none;font-weight:400;margin-right:8px;">&larr; Kembali</a>
    Detail Aset
@endsection

@section('content')

<div class="grid" style="grid-template-columns: 1fr 400px; gap: 24px; align-items: start;">
    <!-- Info Utama -->
    <div class="card">
        <div class="card-header flex-between">
            <div class="card-title">Informasi Aset</div>
            <a href="#" class="btn btn-sm btn-secondary" onclick="alert('Fitur edit aset (demo)')">Edit</a>
        </div>
        <div class="card-body">
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Kode Aset</div>
            <div style="font-weight:700;font-family:monospace;font-size:18px;margin-bottom:16px;">{{ $asset->asset_code }}</div>

            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Nama Aset</div>
            <div style="font-weight:600;font-size:16px;margin-bottom:16px;">{{ $asset->name }}</div>

            <div class="grid grid-2" style="gap:16px; margin-bottom:16px;">
                <div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Kategori</div>
                    <div style="font-weight:500;">{{ $asset->assetCategory?->name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Lokasi</div>
                    <div style="font-weight:500;">{{ $asset->location ?? '-' }}</div>
                </div>
            </div>

            <div class="grid grid-2" style="gap:16px; margin-bottom:16px; padding:12px; background:var(--bg-elevated); border-radius:8px;">
                <div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Harga Beli Awal</div>
                    <div style="font-weight:600;">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Nilai Saat Ini (Buku)</div>
                    <div style="font-weight:600;color:var(--color-primary)">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</div>
                </div>
            </div>

            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Kondisi Saat Ini</div>
            <div style="margin-bottom:16px;">
                @php
                    $condMap = [
                        'good' => ['label' => 'Baik', 'class' => 'badge-success'],
                        'fair' => ['label' => 'Cukup', 'class' => 'badge-warning'],
                        'poor' => ['label' => 'Rusak', 'class' => 'badge-danger'],
                        'disposed' => ['label' => 'Dihapus', 'class' => 'badge-secondary'],
                    ];
                    $c = $condMap[$asset->condition] ?? $condMap['good'];
                @endphp
                <span class="badge {{ $c['class'] }}">{{ $c['label'] }}</span>
            </div>

            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Catatan</div>
            <div>{{ $asset->notes ?? 'Tidak ada catatan khusus.' }}</div>
        </div>
    </div>

    <!-- Riwayat Depresiasi/Perubahan -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Riwayat Perubahan Nilai/Kondisi</div>
        </div>
        <div class="card-body" style="padding:16px;">
            @if($asset->histories && $asset->histories->count() > 0)
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($asset->histories as $hist)
                    <li style="padding-bottom:12px; margin-bottom:12px; border-bottom:1px solid var(--border);">
                        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                            <span style="font-weight:600; font-size:13px;">{{ $hist->type == 'depreciation' ? 'Penyusutan Nilai' : ($hist->type == 'maintenance' ? 'Perawatan' : 'Lainnya') }}</span>
                            <span style="font-size:11px; color:var(--text-muted);">{{ $hist->created_at->format('d M Y') }}</span>
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">Nilai berubah: Rp {{ number_format($hist->value_before, 0, ',', '.') }} &rarr; Rp {{ number_format($hist->value_after, 0, ',', '.') }}</div>
                        @if($hist->notes)
                        <div style="font-size:12px; font-style:italic; margin-top:4px;">"{{ $hist->notes }}"</div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @else
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">
                    Belum ada riwayat tercatat untuk aset ini.
                </div>
                <!-- Tombol aksi manual untuk demo -->
                <button class="btn btn-sm btn-secondary btn-block mt-4" onclick="alert('Demo: Fitur depresiasi otomatis biasanya dipanggil lewat cron scheduler.')">Hitung Depresiasi Manual</button>
            @endif
        </div>
    </div>
</div>

@endsection
