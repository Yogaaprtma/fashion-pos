@extends('layouts.app')

@section('title', 'Detail Stock Opname')
@section('page-title')
    <a href="{{ route('inventory.opname.index') }}" style="color:var(--text-muted);text-decoration:none;font-weight:400;margin-right:8px;">&larr; Kembali</a>
    Detail Opname #{{ $opname->opname_number }}
@endsection

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <div class="grid grid-4">
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Tanggal Dibuat</div>
                <div style="font-weight:600">{{ $opname->created_at->format('d M Y, H:i') }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Penanggung Jawab</div>
                <div style="font-weight:600">{{ $opname->createdBy->name ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Status</div>
                @if($opname->status == 'draft')
                    <span class="badge badge-warning">DRAFT</span>
                @elseif($opname->status == 'completed')
                    <span class="badge badge-success">SELESAI (Approved)</span>
                @endif
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Catatan</div>
                <div>{{ $opname->notes ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header flex-between">
        <div class="card-title">Lembar Kerja (Worksheet)</div>
        @if($opname->status == 'draft')
            <form action="{{ route('inventory.opname.approve', $opname) }}" method="POST" onsubmit="return confirm('Selesaikan opname ini? Stok akan diperbarui sesuai Fisik.')">
                @csrf
                <button type="submit" class="btn btn-success">✅ Selesaikan & Sesuaikan Stok</button>
            </form>
        @endif
    </div>
    <div class="card-body" style="padding:0">
        <!-- Karena ini demo, update per item menggunakan form post standar atau API. 
             Untuk saat ini form sederhana untuk view. -->
        <table class="table">
            <thead>
                <tr>
                    <th>Produk & Varian</th>
                    <th style="text-align:center">Stok Sistem</th>
                    <th style="text-align:center">Stok Fisik (Actual)</th>
                    <th style="text-align:center">Selisih</th>
                    <th>Catatan (Alasan)</th>
                    @if($opname->status == 'draft')
                    <th width="80">Simpan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($opname->items as $item)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $item->productVariant->product->name ?? 'Unknown' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $item->productVariant->variant_label ?? '-' }}</div>
                    </td>
                    <td style="text-align:center;font-weight:600;font-size:16px;">{{ $item->system_qty }}</td>
                    
                    @if($opname->status == 'draft')
                    <!-- Di aplikasi riil, idealnya pakai ajax per baris. 
                         Untuk framework monolitik sederhana kita pakai form per baris atau livewire. -->
                    <td style="text-align:center">
                        <input type="number" form="form-{{$item->id}}" name="actual_qty" class="form-control" value="{{ $item->actual_qty }}" style="width:100px;margin:0 auto;text-align:center">
                    </td>
                    <td style="text-align:center">
                        @php $diff = $item->actual_qty - $item->system_qty; @endphp
                        <span style="color: {{ $diff < 0 ? 'var(--color-danger)' : ($diff > 0 ? 'var(--color-success)' : 'inherit') }};font-weight:600;">
                            {{ $diff > 0 ? '+'.$diff : $diff }}
                        </span>
                    </td>
                    <td>
                        <input type="text" form="form-{{$item->id}}" name="notes" class="form-control" value="{{ $item->notes }}" placeholder="Mis: Hilang, rusak">
                    </td>
                    <td>
                        <!-- Form tersembunyi untuk submit update per item (Opsional untuk implementasi lanjutan) -->
                        <form id="form-{{$item->id}}" action="#" method="POST">
                            @csrf
                            <button type="button" class="btn btn-sm btn-secondary" onclick="alert('Demo: Di aplikasi asli ini akan update via AJAX/Livewire')">Save</button>
                        </form>
                    </td>
                    @else
                    <td style="text-align:center;font-weight:600;font-size:16px;">{{ $item->actual_qty }}</td>
                    <td style="text-align:center">
                        @php $diff = $item->actual_qty - $item->system_qty; @endphp
                        <span style="color: {{ $diff < 0 ? 'var(--color-danger)' : ($diff > 0 ? 'var(--color-success)' : 'inherit') }};font-weight:600;">
                            {{ $diff > 0 ? '+'.$diff : $diff }}
                        </span>
                    </td>
                    <td>{{ $item->notes ?? '-' }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
