<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-container">

    <div id="loginCard" style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 420px;gap:0;border-radius:24px;overflow:hidden;box-shadow:0 25px 60px rgba(15,23,42,.12);border:1px solid #E2E8F0">

        <!-- Left Panel: Branding -->
        <div style="background:linear-gradient(145deg,#1D4ED8 0%,#2563EB 50%,#0EA5E9 100%);padding:48px;display:flex;flex-direction:column;justify-content:space-between;">
            <div>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:48px">
                    <div style="width:52px;height:52px;background:rgba(255,255,255,0.2);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;backdrop-filter:blur(8px)">🏪</div>
                    <div>
                        <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.02em">FashionPOS</div>
                        <div style="font-size:13px;color:rgba(255,255,255,0.7)">Sistem POS Swalayan Pakaian</div>
                    </div>
                </div>

                <div style="margin-bottom:32px">
                    <h1 style="font-size:28px;font-weight:800;color:#fff;line-height:1.3;margin-bottom:12px;letter-spacing:-0.02em">
                        Kelola toko pakaian<br>dengan lebih efisien
                    </h1>
                    <p style="font-size:14px;color:rgba(255,255,255,0.7);line-height:1.7">
                        Sistem POS terintegrasi untuk mencatat transaksi, mengelola stok, dan memantau laporan keuangan secara real-time.
                    </p>
                </div>

                <!-- Features -->
                @foreach(['Transaksi & Cetak Struk Cepat', 'Manajemen Stok & Inventori', 'Laporan Keuangan Real-time', 'Multi-role Akses Karyawan'] as $feature)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                    <div style="width:22px;height:22px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0">✓</div>
                    <span style="font-size:13.5px;color:rgba(255,255,255,0.85);font-weight:500">{{ $feature }}</span>
                </div>
                @endforeach
            </div>

            <div style="font-size:12px;color:rgba(255,255,255,0.4)">
                © {{ date('Y') }} FashionPOS · v2.0
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div style="background:#fff;padding:48px 40px;display:flex;flex-direction:column;justify-content:center;">
            <div style="margin-bottom:32px">
                <h2 style="font-size:22px;font-weight:800;color:#0F172A;margin-bottom:6px;letter-spacing:-0.01em">Masuk ke Akun</h2>
                <p style="font-size:13.5px;color:#64748B">Gunakan email dan password yang terdaftar</p>
            </div>

            @if(session('error'))
            <div class="alert alert-error mb-4">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="admin@fashionpos.id"
                        autocomplete="email"
                        autofocus
                        required>
                    @error('email')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        Password <span class="required">*</span>
                    </label>
                    <div style="position:relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required>
                        <button type="button" onclick="togglePassword()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:2px" id="togglePwdBtn">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <span class="form-check-label" style="font-size:13px">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding:12px;font-size:15px">
                    Masuk
                </button>
            </form>

            <!-- PIN Login divider -->
            <div style="display:flex;align-items:center;gap:12px;margin:24px 0">
                <div style="flex:1;height:1px;background:var(--border)"></div>
                <span style="font-size:12px;color:var(--text-muted)">atau</span>
                <div style="flex:1;height:1px;background:var(--border)"></div>
            </div>

            <a href="{{ route('login.pin.page') }}" class="btn btn-secondary btn-block" style="padding:11px">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Login dengan PIN Kasir
            </a>

            <div style="margin-top:24px;font-size:12px;color:var(--text-muted);text-align:center">
                Hubungi administrator jika lupa password
            </div>
        </div>
    </div>
</div>

<style>
/* Mobile responsive: stack vertically on small screens */
@media (max-width: 700px) {
    #loginCard {
        grid-template-columns: 1fr !important;
        border-radius: 16px !important;
        max-width: 100% !important;
    }
    /* Hide branding panel on very small screens */
    #loginCard > div:first-child {
        display: none !important;
    }
    .auth-container {
        padding: 16px !important;
        align-items: flex-start !important;
        padding-top: 40px !important;
    }
}
</style>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
