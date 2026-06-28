<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }
        
        .auth-container::before {
            content: '';
            position: absolute;
            top: -200px;
            left: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.15) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-container::after {
            content: '';
            position: absolute;
            bottom: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }

        #loginCard {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 440px;
            border-radius: var(--radius-xl);
            overflow: hidden;
            background: rgba(10, 10, 24, 0.6);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border-strong);
            box-shadow: var(--shadow-xl), 0 0 60px rgba(124, 58, 237, 0.08);
            position: relative;
            z-index: 10;
        }

        .login-left {
            background: linear-gradient(135deg, rgba(10, 10, 30, 0.8) 0%, rgba(8, 8, 22, 0.9) 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid var(--border);
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(124, 58, 237, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124, 58, 237, 0.04) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        }

        .login-right {
            background: var(--bg-card);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Mobile responsive */
        @media (max-width: 800px) {
            #loginCard {
                grid-template-columns: 1fr;
                max-width: 440px;
            }
            .login-left { display: none; }
            .auth-container { padding: 16px; align-items: flex-start; padding-top: 40px; }
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div id="loginCard">
        <!-- Left Panel: Branding -->
        <div class="login-left">
            <div style="position: relative; z-index: 1">
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:48px">
                    <div class="sidebar-logo-icon" style="width: 52px; height: 52px; font-size: 24px;">
                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-family: var(--font-sans); font-size:26px;font-weight:800;letter-spacing:-0.02em" class="gradient-text">FashionPOS</div>
                        <div style="font-size:13px;color:var(--text-muted);font-weight:500;">Sistem POS Swalayan Pakaian</div>
                    </div>
                </div>

                <div style="margin-bottom:36px">
                    <h1 style="font-family: var(--font-sans); font-size:32px;font-weight:800;color:var(--text-primary);line-height:1.2;margin-bottom:16px;letter-spacing:-0.5px">
                        Kelola toko pakaian<br>dengan lebih efisien
                    </h1>
                    <p style="font-size:14px;color:var(--text-secondary);line-height:1.7;max-width:320px;">
                        Sistem POS terintegrasi untuk mencatat transaksi, mengelola stok, dan memantau laporan keuangan secara real-time.
                    </p>
                </div>

                <!-- Features -->
                @foreach(['Transaksi & Cetak Struk Cepat', 'Manajemen Stok & Inventori', 'Laporan Keuangan Real-time', 'Multi-role Akses Karyawan'] as $feature)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                    <div style="width:24px;height:24px;background:rgba(124, 58, 237, 0.15);border: 1px solid rgba(124, 58, 237, 0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;color:var(--color-primary-light)">✓</div>
                    <span style="font-size:13.5px;color:var(--text-secondary);font-weight:500">{{ $feature }}</span>
                </div>
                @endforeach
            </div>

            <div style="font-size:12px;color:var(--text-muted);position:relative;z-index:1;">
                © {{ date('Y') }} FashionPOS · v2.0
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="login-right">
            <div style="margin-bottom:32px;text-align:center">
                <h2 style="font-family: var(--font-sans); font-size:24px;font-weight:800;color:var(--text-primary);margin-bottom:6px;letter-spacing:-0.5px">Masuk ke Akun</h2>
                <p style="font-size:13.5px;color:var(--text-muted)">Gunakan email dan password yang terdaftar</p>
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
                    <label class="form-label" for="email">Email <span style="color:var(--color-danger)">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control {{ $errors->has('email') ? 'error' : '' }}"
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
                        Password <span style="color:var(--color-danger)">*</span>
                    </label>
                    <div style="position:relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'error' : '' }}"
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

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="remember" id="remember" style="accent-color:var(--color-primary);width:16px;height:16px">
                        <span style="font-size:13px;color:var(--text-secondary)">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-xl">
                    Masuk
                </button>
            </form>

            <!-- PIN Login divider -->
            <div style="display:flex;align-items:center;gap:12px;margin:24px 0">
                <div style="flex:1;height:1px;background:var(--border)"></div>
                <span style="font-size:12px;color:var(--text-muted);font-weight:600;letter-spacing:1px;text-transform:uppercase">atau</span>
                <div style="flex:1;height:1px;background:var(--border)"></div>
            </div>

            <a href="{{ route('login.pin.page') }}" class="btn btn-secondary btn-block btn-lg" style="gap:8px">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
