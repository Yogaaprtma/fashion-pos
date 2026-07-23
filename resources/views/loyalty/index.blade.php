@extends('layouts.app')

@section('title', 'Program Loyalty Member')
@section('page-title', 'Program Loyalty Member')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-header-title">⭐ Program Loyalty Member</h1>
        <p class="page-header-subtitle">Kelola poin reward pelanggan setia Anda. Poin dikumpulkan dari setiap transaksi dan bisa ditukarkan dengan diskon.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="card" style="padding:20px; text-align:center; border-top:3px solid var(--color-primary);">
        <div style="font-size:28px; font-weight:800; color:var(--color-primary);">{{ number_format($stats['total_members']) }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Total Member</div>
    </div>
    <div class="card" style="padding:20px; text-align:center; border-top:3px solid #F59E0B;">
        <div style="font-size:28px; font-weight:800; color:#F59E0B;">{{ number_format($stats['total_gold']) }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">🥇 Gold Member</div>
    </div>
    <div class="card" style="padding:20px; text-align:center; border-top:3px solid #94A3B8;">
        <div style="font-size:28px; font-weight:800; color:#94A3B8;">{{ number_format($stats['total_silver']) }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">🥈 Silver Member</div>
    </div>
    <div class="card" style="padding:20px; text-align:center; border-top:3px solid #CD7C2F;">
        <div style="font-size:28px; font-weight:800; color:#CD7C2F;">{{ number_format($stats['total_bronze']) }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">🥉 Bronze Member</div>
    </div>
    <div class="card" style="padding:20px; text-align:center; border-top:3px solid #10B981;">
        <div style="font-size:28px; font-weight:800; color:#10B981;">{{ number_format($stats['total_points']) }}</div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Total Poin Aktif</div>
    </div>
</div>

{{-- Tier Info --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <h3 style="font-size:14px; font-weight:700; margin-bottom:12px;">📊 Level Member & Ketentuan Poin</h3>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
            <div style="padding:14px 16px; border-radius:var(--radius-lg); background:linear-gradient(135deg,#CD7C2F22,#CD7C2F11); border:1px solid #CD7C2F44;">
                <div style="font-weight:700; color:#CD7C2F; margin-bottom:6px;">🥉 Bronze</div>
                <div style="font-size:12px; color:var(--text-muted);">0 – 999 poin</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Rp 1.000 = 1 Poin · 100 Poin = Rp 1.000 diskon</div>
            </div>
            <div style="padding:14px 16px; border-radius:var(--radius-lg); background:linear-gradient(135deg,#94A3B822,#94A3B811); border:1px solid #94A3B844;">
                <div style="font-weight:700; color:#64748B; margin-bottom:6px;">🥈 Silver</div>
                <div style="font-size:12px; color:var(--text-muted);">1.000 – 4.999 poin</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Rp 1.000 = 1,5 Poin · Bonus event khusus</div>
            </div>
            <div style="padding:14px 16px; border-radius:var(--radius-lg); background:linear-gradient(135deg,#F59E0B22,#F59E0B11); border:1px solid #F59E0B44;">
                <div style="font-weight:700; color:#F59E0B; margin-bottom:6px;">🥇 Gold</div>
                <div style="font-size:12px; color:var(--text-muted);">5.000+ poin</div>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Rp 1.000 = 2 Poin · Akses promo eksklusif</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <div class="search-input" style="flex:1; min-width:220px;">
                <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / no. HP..." class="form-control" style="padding-left:36px;">
            </div>
            <select name="tier" class="form-control" style="width:160px;">
                <option value="">Semua Tier</option>
                <option value="gold" {{ request('tier')=='gold' ? 'selected' : '' }}>🥇 Gold (5000+)</option>
                <option value="silver" {{ request('tier')=='silver' ? 'selected' : '' }}>🥈 Silver (1000+)</option>
                <option value="bronze" {{ request('tier')=='bronze' ? 'selected' : '' }}>🥉 Bronze</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['search','tier']))
                <a href="{{ route('loyalty.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- Member Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Level</th>
                    <th style="text-align:right;">Poin Aktif</th>
                    <th style="text-align:right;">Total Belanja</th>
                    <th style="text-align:center;">Transaksi</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $customer->name }}</div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ $customer->phone ?? 'Tidak ada nomor' }}</div>
                    </td>
                    <td>
                        @php $tier = $customer->member_tier; @endphp
                        <span style="font-weight:700; color:{{ $customer->member_tier_color }}; font-size:13px;">
                            {{ $tier === 'Gold' ? '🥇' : ($tier === 'Silver' ? '🥈' : '🥉') }} {{ $tier }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <span style="font-size:16px; font-weight:800; color:var(--color-primary);">
                            {{ number_format($customer->points ?? 0) }}
                        </span>
                        <div style="font-size:11px; color:var(--text-muted);">poin</div>
                    </td>
                    <td style="text-align:right; font-weight:600;">
                        Rp {{ number_format($customer->total_spent_sum ?? 0, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-secondary">{{ $customer->total_transactions ?? 0 }}x</span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('loyalty.show', $customer) }}" class="btn btn-sm btn-ghost" title="Riwayat Poin">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Riwayat
                            </a>
                            <button onclick="openAdjustModal({{ $customer->id }}, '{{ $customer->name }}', {{ $customer->points ?? 0 }})" class="btn btn-sm btn-outline-primary" title="Sesuaikan Poin">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Sesuaikan
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">⭐</div>
                            <div class="empty-state-title">Belum ada member terdaftar</div>
                            <div class="empty-state-desc">Pelanggan yang sudah bertanda member akan tampil di sini beserta poin reward mereka.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>

{{-- Adjust Modal --}}
<div class="modal-overlay" id="adjustModal" style="display:none;">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <h3 class="modal-title">Sesuaikan Poin Member</h3>
            <button class="modal-close" onclick="document.getElementById('adjustModal').style.display='none'">&times;</button>
        </div>
        <form id="adjustForm" method="POST">
            @csrf
            @method('POST')
            <div class="modal-body">
                <div style="background:var(--bg-hover); border-radius:var(--radius-md); padding:12px 16px; margin-bottom:16px;">
                    <div id="adjustCustomerName" style="font-weight:700; font-size:14px;"></div>
                    <div id="adjustCurrentPoints" style="font-size:13px; color:var(--text-muted);"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Poin (+ untuk tambah, - untuk kurangi)</label>
                    <input type="number" name="amount" id="adjustAmount" class="form-control" placeholder="Contoh: 100 atau -50" required>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Gunakan nilai negatif untuk mengurangi poin</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan Penyesuaian</label>
                    <input type="text" name="description" class="form-control" placeholder="Contoh: Bonus event hari jadi toko" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('adjustModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAdjustModal(customerId, name, currentPoints) {
    document.getElementById('adjustCustomerName').textContent = name;
    document.getElementById('adjustCurrentPoints').textContent = 'Poin saat ini: ' + currentPoints.toLocaleString('id-ID') + ' poin';
    document.getElementById('adjustForm').action = '/loyalty/' + customerId + '/adjust';
    document.getElementById('adjustAmount').value = '';
    document.getElementById('adjustModal').style.display = 'flex';
}
</script>
@endpush
