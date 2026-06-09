@extends('layouts.app')

@section('title', 'Buka Shift Kasir')

@section('content')

<div class="card" style="max-width:400px; margin: 40px auto; padding: 20px;">
    <div style="text-align:center; margin-bottom: 24px;">
        <div style="font-size:40px; margin-bottom:8px;">💰</div>
        <h2 style="margin:0; font-size: 20px;">Buka Shift Kasir</h2>
        <p style="color:var(--text-muted); font-size:13px;">Silakan masukkan nominal uang tunai modal awal di laci kasir.</p>
    </div>

    <form action="{{ route('pos.session.start') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" style="font-size:14px; font-weight:600;">Saldo Awal (Rp) *</label>
            <input type="number" name="opening_balance" class="form-control" style="font-size:18px; text-align:right;" value="0" min="0" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 15px; margin-top: 24px;">
            Mulai Jualan (Buka Kasir)
        </button>
    </form>
</div>

@endsection
