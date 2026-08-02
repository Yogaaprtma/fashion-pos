@extends('layouts.app')

@section('title', 'Integrasi & Webhook E-Commerce')
@section('page-title', 'Integrasi E-Commerce (Omnichannel)')

@section('content')

<div class="page-header-enhanced">
    <div class="page-header-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">›</span>
        <span>Pengaturan</span>
        <span class="sep">›</span>
        <span>Integrasi E-Commerce</span>
    </div>
    <div class="page-header-main">
        <div style="display:flex;align-items:flex-start;gap:16px;">
            <div class="page-icon-box" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8);">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div>
                <h1>🌐 Hub Integrasi & Sync Stok E-Commerce</h1>
                <p class="subtitle">Hubungkan stok toko fisik FashionPOS dengan Toko Online (Shopee, Tokopedia, WooCommerce, TikTok Shop).</p>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">
                + Tambah Kanal Integrasi
            </button>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

    {{-- Left: Active Integrations --}}
    <div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">Kanal E-Commerce Terhubung</h3></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>Kanal</th>
                                <th>API Key & Webhook</th>
                                <th>Arah Sync</th>
                                <th>Sync Terakhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($integrations as $item)
                            <tr>
                                <td>
                                    <div style="font-weight:700;font-size:14px;">🛍️ {{ $item->channel_name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);">
                                        {{ $item->auto_deduct_stock ? '⚡ Otomatis Potong Stok POS' : '🔒 Hanya Sync Baca' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:11px;font-family:monospace;background:var(--bg-elevated);padding:3px 6px;border-radius:4px;">
                                        Key: {{ Str::limit($item->api_key, 18) }}...
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">
                                        Secret: {{ Str::limit($item->webhook_secret, 14) }}...
                                    </div>
                                </td>
                                <td>
                                    @if($item->sync_direction == 'bidirectional')
                                    <span class="badge badge-indigo">🔄 Dua Arah</span>
                                    @elseif($item->sync_direction == 'pos_to_online')
                                    <span class="badge badge-secondary">📤 POS → Online</span>
                                    @else
                                    <span class="badge badge-secondary">📥 Online → POS</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->last_synced_at)
                                    <span style="font-size:12px;">{{ $item->last_synced_at->diffForHumans() }}</span>
                                    @else
                                    <span style="color:var(--text-muted);font-size:12px;">Belum pernah</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                    @else
                                    <span class="badge badge-secondary">Non-aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        <form method="POST" action="{{ route('settings.integrations.toggle', $item) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                {{ $item->is_active ? 'Matikan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('settings.integrations.destroy', $item) }}" onsubmit="return confirm('Hapus integrasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">✕</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada kanal integrasi. Klik "+ Tambah Kanal Integrasi".</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- API Documentation Card --}}
        <div class="card" style="margin-top:20px;">
            <div class="card-header"><h3 class="card-title">📖 Panduan Endpoint Webhook API</h3></div>
            <div class="card-body" style="font-size:13px;line-height:1.6;">
                <div style="font-weight:700;margin-bottom:4px;color:var(--color-primary)">1. Cek Real-time Stok POS</div>
                <pre style="background:var(--bg-elevated);padding:8px;border-radius:6px;font-size:11px;overflow-x:auto;">GET /api/v1/external/stock-sync
Header: X-API-KEY: [API_KEY_ANDA]</pre>

                <div style="font-weight:700;margin-top:12px;margin-bottom:4px;color:var(--color-primary)">2. Webhook Pesanan Toko Online (Potong Stok POS)</div>
                <pre style="background:var(--bg-elevated);padding:8px;border-radius:6px;font-size:11px;overflow-x:auto;">POST /api/v1/external/orders
Header: X-API-KEY: [API_KEY_ANDA]
Payload: { "order_ref": "SHOPEE-99012", "items": [{ "sku": "TSH-BLK-M", "qty": 2 }] }</pre>
            </div>
        </div>
    </div>

    {{-- Right: Activity Logs --}}
    <div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">📜 Log Aktivitas Webhook</h3></div>
            <div class="card-body p-0">
                <div style="max-height:480px;overflow-y:auto;">
                    <table class="table align-middle" style="font-size:12px;">
                        <tbody>
                            @forelse($recentLogs as $log)
                            <tr>
                                <td style="padding:10px 12px;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;">
                                        <strong>{{ $log->integration->channel_name ?? 'Kanal' }}</strong>
                                        <span class="badge {{ $log->status == 'success' ? 'badge-success' : 'badge-danger' }}">{{ $log->status }}</span>
                                    </div>
                                    <div style="color:var(--text-muted);font-size:11px;margin-top:2px;">{{ $log->response_message }}</div>
                                    <div style="color:var(--text-muted);font-size:10px;margin-top:2px;">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr><td class="text-center py-4 text-muted">Belum ada riwayat aktivitas log API.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;backdrop-filter:blur(4px);">
    <div style="max-width:480px;margin:80px auto;background:var(--bg-card);border-radius:16px;padding:24px;box-shadow:var(--shadow-xl);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="margin:0;font-size:18px;">+ Tambah Kanal Integrasi</h3>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <form method="POST" action="{{ route('settings.integrations.store') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label class="form-label">Nama Kanal / Platform <span style="color:red">*</span></label>
                <input type="text" name="channel_name" class="form-control" required placeholder="Contoh: Toko Shopee Official, Tokopedia, WooCommerce Website">
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Arah Sinkronisasi</label>
                <select name="sync_direction" class="form-select">
                    <option value="bidirectional">🔄 Dua Arah (POS ↔ Online)</option>
                    <option value="pos_to_online">📤 POS → Online (Kirim Stok POS ke Toko Online)</option>
                    <option value="online_to_pos">📥 Online → POS (Potong Stok POS saat ada Order Online)</option>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="auto_deduct_stock" value="1" checked>
                    Potong stok toko fisik secara otomatis saat ada order e-commerce
                </label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;height:42px;">Buat Kunci API & Webhook</button>
        </form>
    </div>
</div>

@endsection
