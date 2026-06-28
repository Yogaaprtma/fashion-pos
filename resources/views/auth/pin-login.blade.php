<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PIN Kasir — FashionPOS</title>
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

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: rgba(10, 10, 24, 0.7);
            backdrop-filter: blur(24px) saturate(150%);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-xl), 0 0 60px rgba(124, 58, 237, 0.1);
            position: relative;
            z-index: 10;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--color-primary), var(--color-accent), transparent);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            opacity: 0.8;
        }

        .auth-logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(6, 182, 212, 0.1));
            border: 1px solid rgba(124, 58, 237, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--text-primary);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.2);
            margin: 0 auto 20px;
        }

        .pin-display {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .pin-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--bg-elevated);
            border: 2px solid var(--border-strong);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .pin-dot.filled {
            background: var(--color-primary-light);
            border-color: var(--color-primary);
            box-shadow: 0 0 12px var(--color-primary-glow);
            transform: scale(1.1);
        }

        .numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .numpad-btn {
            aspect-ratio: 1;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
            font-family: var(--font-sans);
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .numpad-btn:hover {
            background: rgba(124, 58, 237, 0.15);
            border-color: rgba(124, 58, 237, 0.4);
            color: var(--color-primary-light);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
            transform: translateY(-2px);
        }
        
        .numpad-btn:active {
            transform: scale(0.95);
        }

    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card" style="text-align:center;">

        <!-- Logo -->
        <div style="margin-bottom:28px;">
            <div class="auth-logo-icon">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                </svg>
            </div>
            <h1 style="font-family:var(--font-sans); font-size:22px; font-weight:800; color:var(--text-primary); margin-bottom:6px; letter-spacing:-0.5px">Login PIN Kasir</h1>
            <p style="font-size:13px; color:var(--text-muted);">Masukkan PIN 4-6 digit untuk memulai shift</p>
        </div>

        @if($errors->has('pin'))
        <div class="alert alert-error mb-4" style="text-align:left;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $errors->first('pin') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.pin') }}" id="pinForm">
            @csrf
            <input type="hidden" name="pin" id="pinValue">

            <!-- PIN Dots Display -->
            <div class="pin-display">
                <div class="pin-dot" id="dot-0"></div>
                <div class="pin-dot" id="dot-1"></div>
                <div class="pin-dot" id="dot-2"></div>
                <div class="pin-dot" id="dot-3"></div>
                <div class="pin-dot" id="dot-4"></div>
                <div class="pin-dot" id="dot-5"></div>
            </div>

            <!-- Numpad -->
            <div class="numpad">
                @foreach([1,2,3,4,5,6,7,8,9] as $num)
                <button type="button" class="numpad-btn" onclick="addDigit('{{ $num }}')">{{ $num }}</button>
                @endforeach
                <button type="button" class="numpad-btn" onclick="clearPin()" style="font-size:16px; font-weight:700; color:var(--text-muted);">C</button>
                <button type="button" class="numpad-btn" onclick="addDigit('0')">0</button>
                <button type="button" class="numpad-btn" onclick="deleteDigit()" style="font-size:20px; color:var(--color-danger-text)">⌫</button>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary btn-block btn-xl" id="submitBtn"
                    style="margin-top:24px; opacity:0.4; pointer-events:none;">
                Masuk
            </button>
        </form>

        <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
            <a href="{{ route('login') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:color 0.2s" onmouseover="this.style.color='var(--color-primary-light)'" onmouseout="this.style.color='var(--text-muted)'">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Login Email
            </a>
        </div>
    </div>
</div>

<script>
    let pin = '';
    const maxLen = 6;
    const minLen = 4;

    function updateDisplay() {
        for (let i = 0; i < maxLen; i++) {
            const dot = document.getElementById('dot-' + i);
            dot.classList.toggle('filled', i < pin.length);
        }

        const submitBtn = document.getElementById('submitBtn');
        if (pin.length >= minLen) {
            submitBtn.style.opacity = '1';
            submitBtn.style.pointerEvents = 'auto';
            submitBtn.style.boxShadow = '0 6px 28px rgba(124, 58, 237, 0.55)';
        } else {
            submitBtn.style.opacity = '0.4';
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.boxShadow = 'none';
        }

        document.getElementById('pinValue').value = pin;
    }

    function addDigit(d) {
        if (pin.length >= maxLen) return;
        pin += d;
        updateDisplay();

        // Auto-submit when 6 digits entered
        if (pin.length === maxLen) {
            setTimeout(() => document.getElementById('pinForm').submit(), 300);
        }
    }

    function deleteDigit() {
        pin = pin.slice(0, -1);
        updateDisplay();
    }

    function clearPin() {
        pin = '';
        updateDisplay();
    }

    // Keyboard support
    document.addEventListener('keydown', (e) => {
        if (e.key >= '0' && e.key <= '9') addDigit(e.key);
        else if (e.key === 'Backspace') deleteDigit();
        else if (e.key === 'Escape') clearPin();
        else if (e.key === 'Enter' && pin.length >= minLen) document.getElementById('pinForm').submit();
    });
</script>

</body>
</html>
