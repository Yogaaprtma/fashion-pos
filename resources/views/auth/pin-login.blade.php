<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PIN Kasir — FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-container">
    <div class="auth-card" style="max-width:380px; text-align:center;">

        <!-- Logo -->
        <div style="margin-bottom:28px;">
            <div class="auth-logo-icon" style="margin:0 auto 14px;">🏪</div>
            <h1 style="font-size:20px; font-weight:800; color:var(--text-primary); margin-bottom:4px;">Login PIN Kasir</h1>
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
            <div class="pin-display" style="margin-bottom:28px;">
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
                <button type="button" class="numpad-btn" onclick="clearPin()" style="font-size:13px; font-weight:600; color:var(--text-muted);">C</button>
                <button type="button" class="numpad-btn" onclick="addDigit('0')">0</button>
                <button type="button" class="numpad-btn" onclick="deleteDigit()" style="font-size:18px;">⌫</button>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary btn-block" id="submitBtn"
                    style="margin-top:20px; padding:13px; font-size:15px; opacity:0.4; pointer-events:none;">
                Masuk
            </button>
        </form>

        <div style="margin-top:20px; padding-top:18px; border-top:1px solid var(--border);">
            <a href="{{ route('login') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        } else {
            submitBtn.style.opacity = '0.4';
            submitBtn.style.pointerEvents = 'none';
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
