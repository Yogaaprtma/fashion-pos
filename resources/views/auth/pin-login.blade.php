<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PIN Kasir — FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&amp;family=JetBrains+Mono:wght@400;500;600;700&amp;family=Outfit:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=3.1.0">
    <style>
        .auth-container {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 28px;
            position: relative;
            background:
                radial-gradient(circle at 8% 10%, rgba(59, 130, 246, .20), transparent 26rem),
                radial-gradient(circle at 92% 90%, rgba(139, 92, 246, .16), transparent 27rem),
                radial-gradient(circle at 72% 2%, rgba(79, 70, 229, .09), transparent 21rem),
                #F8FAFC;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: -120px;
            right: 9%;
            width: 260px;
            height: 260px;
            border: 1px solid rgba(79, 70, 229, .18);
            box-shadow: 0 0 0 45px rgba(255,255,255,.28), 0 0 0 90px rgba(59,130,246,.055);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-container::after {
            content: '';
            position: absolute;
            bottom: -90px;
            left: 7%;
            width: 190px;
            height: 190px;
            background: rgba(255,255,255,.28);
            border: 1px solid rgba(139, 92, 246, .16);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(22px) saturate(140%);
            border: 1px solid #E5E7EB;
            border-radius: 26px;
            padding: 38px;
            box-shadow: 0 28px 70px rgba(30, 41, 59, .15), 0 8px 24px rgba(79, 70, 229, .07);
            position: relative;
            z-index: 10;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3B82F6, #4F46E5, #8B5CF6, transparent);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            opacity: 0.8;
        }

        .auth-logo-icon {
            width: 60px;
            height: 60px;
            color: #fff;
            background: linear-gradient(145deg, #3B82F6, #4F46E5 62%, #7C3AED);
            border: 1px solid rgba(79,70,229,.24);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 12px 28px rgba(79,70,229,.30);
            margin: 0 auto 20px;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .auth-logo-icon:hover {
            transform: translateY(-2px) rotate(-3deg) scale(1.04);
            box-shadow: 0 17px 32px rgba(79,70,229,.36);
        }

        .pin-display {
            display: flex;
            justify-content: center;
            gap: 11px;
            margin: 4px 0 27px;
        }

        .pin-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #F8FAFC;
            border: 2px solid #CBD5E1;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .pin-dot.filled {
            background: linear-gradient(145deg, #3B82F6, #4F46E5);
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79,70,229,.13), 0 5px 12px rgba(79,70,229,.20);
            transform: scale(1.1);
        }

        .numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .numpad-btn {
            aspect-ratio: 1;
            border-radius: var(--radius-lg);
            border: 1px solid #E5E7EB;
            background: #FFFFFF;
            font-family: var(--font-sans);
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(15,23,42,.035);
            transition: transform .22s cubic-bezier(.4, 0, .2, 1), color .22s ease, background .22s ease, border-color .22s ease, box-shadow .22s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .numpad-btn:hover {
            background: linear-gradient(145deg, #EEF2FF, #EFF6FF);
            border-color: #A5B4FC;
            color: #4338CA;
            box-shadow: 0 10px 22px rgba(79,70,229,.14);
            transform: translateY(-3px);
        }
        
        .numpad-btn:active {
            color: #FFFFFF;
            background: linear-gradient(145deg, #3B82F6, #4F46E5);
            border-color: #4F46E5;
            box-shadow: 0 5px 14px rgba(79,70,229,.24);
            transform: scale(0.96);
        }

        .auth-card h1 { color: #111827 !important; }
        .auth-card h1 + p { color: #6B7280 !important; }
        .auth-card > div:last-child a { color: #4F46E5 !important; font-weight: 700; }
        .auth-card > div:last-child a:hover { color: #3730A3 !important; }

        @media (max-width: 520px) {
            .auth-container { padding: 0; background: #fff; }
            .auth-card {
                min-height: 100dvh;
                max-width: none;
                display: flex;
                flex-direction: column;
                justify-content: center;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 30px 24px;
            }
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
            submitBtn.style.boxShadow = '0 14px 28px rgba(79, 70, 229, 0.30)';
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
