<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f7f9fc">
    <title>Masuk — FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #111827;
            --ink-soft: #4B5563;
            --ink-muted: #6B7280;
            --line: #E5E7EB;
            --surface: #ffffff;
            --surface-soft: #F8FAFC;
            --primary: #4F46E5;
            --primary-dark: #3730A3;
            --primary-soft: #EEF2FF;
            --secondary: #8B5CF6;
            --info: #3B82F6;
            --success: #047857;
            --success-soft: #ECFDF5;
            --danger: #DC2626;
            --danger-soft: #FEF2F2;
            --shadow: 0 28px 70px rgba(30, 41, 59, .14), 0 8px 24px rgba(79, 70, 229, .06);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        * {
            margin: 0;
        }

        html {
            min-width: 320px;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            color: var(--ink);
            background:
                radial-gradient(circle at 7% 12%, rgba(59, 130, 246, .17), transparent 28rem),
                radial-gradient(circle at 92% 88%, rgba(139, 92, 246, .12), transparent 28rem),
                radial-gradient(circle at 74% 2%, rgba(79, 70, 229, .08), transparent 22rem),
                #F8FAFC;
            font-family: "Manrope", "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        button, input {
            font: inherit;
        }

        button, a {
            -webkit-tap-highlight-color: transparent;
        }

        .auth-page {
            isolation: isolate;
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            position: relative;
            padding: 32px;
        }

        .auth-page::before,
        .auth-page::after {
            content: "";
            position: fixed;
            z-index: -1;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(.5px);
        }

        .auth-page::before {
            width: 15rem;
            height: 15rem;
            top: -8rem;
            right: 10%;
            border: 1px solid rgba(79, 70, 229, .15);
            box-shadow: 0 0 0 2.8rem rgba(255, 255, 255, .28), 0 0 0 5.4rem rgba(59, 130, 246, .045);
        }

        .auth-page::after {
            width: 11rem;
            height: 11rem;
            bottom: -6rem;
            left: 6%;
            background: rgba(255, 255, 255, .40);
            border: 1px solid rgba(139, 92, 246, .14);
        }

        .auth-shell {
            width: min(1160px, 100%);
            min-height: min(730px, calc(100dvh - 64px));
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(420px, .96fr);
            overflow: hidden;
            position: relative;
            background: rgba(255, 255, 255, .96);
            border: 1px solid #E5E7EB;
            border-radius: 32px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            text-decoration: none;
            transition: transform .25s cubic-bezier(.4, 0, .2, 1), opacity .25s ease;
        }

        .brand-mark:hover { transform: translateY(-2px); }

        .brand-mark__icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            color: #fff;
            border-radius: 13px;
            background: linear-gradient(145deg, #3B82F6, #4F46E5 62%, #7C3AED);
            box-shadow: 0 10px 24px rgba(79, 70, 229, .28);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .brand-mark:hover .brand-mark__icon { transform: rotate(-3deg) scale(1.04); box-shadow: 0 14px 28px rgba(79, 70, 229, .34); }

        .brand-mark__name {
            display: block;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -.04em;
            line-height: 1.05;
        }

        .brand-mark__name span {
            color: var(--primary);
        }

        .brand-mark__caption {
            display: block;
            margin-top: 4px;
            color: #64748B;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .showcase {
            min-width: 0;
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 45px 48px 38px;
            overflow: hidden;
            background:
                linear-gradient(rgba(79, 70, 229, .055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79, 70, 229, .055) 1px, transparent 1px),
                linear-gradient(145deg, #F8FAFF 0%, #EEF2FF 46%, #EFF6FF 72%, #F5F3FF 100%);
            background-size: 28px 28px, 28px 28px, auto;
            border-right: 1px solid #E5E7EB;
        }

        .showcase::before {
            content: "";
            width: 360px;
            height: 360px;
            position: absolute;
            right: -150px;
            top: -140px;
            border-radius: 50%;
            background: rgba(99, 102, 241, .16);
            box-shadow: 0 0 0 52px rgba(255, 255, 255, .22), 0 0 0 104px rgba(59, 130, 246, .055);
        }

        .showcase::after {
            content: "";
            width: 300px;
            height: 300px;
            position: absolute;
            left: -150px;
            bottom: -130px;
            border-radius: 50%;
            background: rgba(139, 92, 246, .13);
            filter: blur(58px);
            pointer-events: none;
        }

        .showcase__copy {
            max-width: 490px;
            position: relative;
            z-index: 1;
            margin-top: clamp(48px, 8vh, 84px);
        }

        .eyebrow {
            width: max-content;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 17px;
            padding: 8px 12px;
            color: #4338CA;
            background: rgba(255, 255, 255, .84);
            border: 1px solid #C7D2FE;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            box-shadow: 0 7px 18px rgba(79, 70, 229, .07);
        }

        .eyebrow__dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .14);
        }

        .showcase h1 {
            max-width: 470px;
            color: #111827;
            font-size: clamp(35px, 3.1vw, 48px);
            font-weight: 800;
            letter-spacing: -.055em;
            line-height: 1.1;
        }

        .showcase h1 span {
            color: transparent;
            background: linear-gradient(115deg, #4F46E5, #7C3AED 55%, #3B82F6);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .showcase__lead {
            max-width: 440px;
            margin-top: 18px;
            color: #475569;
            font-size: 14px;
            line-height: 1.8;
        }

        .dashboard-preview {
            width: min(470px, 91%);
            min-height: 224px;
            position: relative;
            z-index: 1;
            margin-top: 38px;
            padding: 18px;
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(255, 255, 255, .98);
            border-radius: 22px;
            box-shadow: 0 24px 52px rgba(49, 46, 129, .15), inset 0 0 0 1px rgba(199, 210, 254, .65);
            transform: perspective(900px) rotateY(-2deg) rotateX(1deg);
            transition: transform .3s cubic-bezier(.4, 0, .2, 1), box-shadow .3s ease;
        }

        .dashboard-preview:hover {
            transform: perspective(900px) rotateY(0) rotateX(0) translateY(-4px);
            box-shadow: 0 30px 60px rgba(49, 46, 129, .19), inset 0 0 0 1px rgba(165, 180, 252, .72);
        }

        .preview-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .preview-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #1E293B;
            font-size: 11px;
            font-weight: 800;
        }

        .preview-title::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 3px;
            background: #4F46E5;
            box-shadow: 11px 0 0 #60A5FA;
        }

        .preview-status {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #047857;
            font-size: 9px;
            font-weight: 800;
        }

        .preview-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 0 3px #D1FAE5;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
        }

        .metric-card {
            min-width: 0;
            padding: 12px;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .035);
            transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
        }

        .metric-card:hover { transform: translateY(-2px); border-color: #C7D2FE; box-shadow: 0 8px 18px rgba(79, 70, 229, .09); }
        .metric-card:nth-child(1) { border-top: 2px solid #4F46E5; }
        .metric-card:nth-child(2) { border-top: 2px solid #10B981; }
        .metric-card:nth-child(3) { border-top: 2px solid #3B82F6; }

        .metric-label {
            color: #64748B;
            font-size: 8px;
            font-weight: 700;
            white-space: nowrap;
        }

        .metric-value {
            margin-top: 5px;
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .metric-trend {
            margin-top: 4px;
            color: #059669;
            font-size: 8px;
            font-weight: 800;
        }

        .chart-panel {
            display: grid;
            grid-template-columns: 1fr 92px;
            gap: 15px;
            margin-top: 14px;
            padding: 14px 12px 10px;
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 13px;
        }

        .bars {
            height: 58px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 0 3px;
            border-bottom: 1px solid #E2E8F0;
        }

        .bar {
            width: 100%;
            height: var(--height);
            min-height: 8px;
            border-radius: 5px 5px 2px 2px;
            background: linear-gradient(180deg, #4F46E5, #93C5FD);
        }

        .donut-wrap {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .donut {
            width: 46px;
            height: 46px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(#4F46E5 0 66%, #3B82F6 66% 85%, #D1FAE5 85% 100%);
        }

        .donut::after {
            content: "";
            width: 27px;
            height: 27px;
            border-radius: 50%;
            background: #F8FAFC;
        }

        .donut-copy strong,
        .donut-copy span {
            display: block;
        }

        .donut-copy strong {
            color: #111827;
            font-size: 11px;
        }

        .donut-copy span {
            color: #64748B;
            font-size: 7px;
        }

        .floating-note {
            position: absolute;
            right: -41px;
            bottom: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 13px;
            background: rgba(255, 255, 255, .96);
            border: 1px solid #E5E7EB;
            border-radius: 13px;
            box-shadow: 0 16px 32px rgba(30, 41, 59, .14), 0 4px 12px rgba(79, 70, 229, .06);
            animation: float 4s ease-in-out infinite;
        }

        .floating-note__icon {
            width: 29px;
            height: 29px;
            display: grid;
            place-items: center;
            color: #047857;
            background: #D1FAE5;
            border-radius: 9px;
        }

        .floating-note strong,
        .floating-note span {
            display: block;
            white-space: nowrap;
        }

        .floating-note strong {
            color: #111827;
            font-size: 9px;
        }

        .floating-note span {
            margin-top: 2px;
            color: #64748B;
            font-size: 7px;
        }

        .showcase__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: relative;
            z-index: 1;
            margin-top: auto;
            padding-top: 30px;
            color: #64748B;
            font-size: 10px;
            font-weight: 600;
        }

        .showcase__footer span:last-child {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .showcase__footer span:last-child::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10B981;
        }

        .login-panel {
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 42px 48px;
            background:
                radial-gradient(circle at 100% 0%, rgba(99, 102, 241, .08), transparent 17rem),
                linear-gradient(180deg, #FFFFFF, #FBFCFF);
        }

        .login-panel::before {
            content: "";
            width: 210px;
            height: 210px;
            position: absolute;
            right: -115px;
            bottom: -105px;
            border: 1px solid rgba(79, 70, 229, .10);
            border-radius: 50%;
            box-shadow: 0 0 0 34px rgba(59,130,246,.025), 0 0 0 68px rgba(139,92,246,.018);
            pointer-events: none;
        }

        .mobile-brand {
            display: none;
        }

        .login-content {
            width: min(100%, 440px);
            position: relative;
            z-index: 1;
            padding: 30px;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 22px;
            box-shadow: 0 18px 48px rgba(30, 41, 59, .10), 0 4px 14px rgba(79, 70, 229, .045);
        }

        .login-kicker {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 13px;
            color: #4F46E5;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .login-kicker svg {
            color: var(--primary);
        }

        .login-heading h2 {
            color: #111827;
            font-size: clamp(28px, 2.4vw, 36px);
            font-weight: 800;
            letter-spacing: -.05em;
            line-height: 1.18;
        }

        .login-heading p {
            margin-top: 10px;
            color: #6B7280;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 22px;
            padding: 12px 13px;
            border: 1px solid;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.55;
        }

        .alert svg {
            flex: 0 0 auto;
            margin-top: 1px;
        }

        .alert--error {
            color: #B91C1C;
            background: var(--danger-soft);
            border-color: #FECACA;
        }

        .alert--success {
            color: var(--success);
            background: var(--success-soft);
            border-color: #A7F3D0;
        }

        .login-form {
            margin-top: 28px;
        }

        .field {
            margin-bottom: 19px;
        }

        .field__label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #1F2937;
            font-size: 12.5px;
            font-weight: 800;
        }

        .field__required {
            color: var(--danger);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap__icon {
            width: 18px;
            height: 18px;
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 15px;
            color: #6366F1;
            transform: translateY(-50%);
            pointer-events: none;
            opacity: .78;
            transition: color .22s ease, opacity .22s ease, transform .22s ease;
        }

        .field__input {
            width: 100%;
            height: 52px;
            padding: 0 47px 0 45px;
            color: #111827;
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 13px;
            outline: none;
            font-size: 13px;
            font-weight: 600;
            transition: border-color .22s ease, box-shadow .22s ease, background .22s ease, transform .22s ease;
        }

        .field__input::placeholder {
            color: #6B7280;
            font-weight: 500;
        }

        .field__input:hover {
            border-color: #C7D2FE;
            background: #fff;
        }

        .field__input:focus {
            background: #fff;
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .11), 0 8px 18px rgba(79, 70, 229, .06);
        }

        .input-wrap:focus-within .input-wrap__icon {
            color: #4F46E5;
            opacity: 1;
            transform: translateY(-50%) scale(1.06);
        }

        .field__input.is-invalid {
            border-color: #EF4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .09);
        }

        .field__error {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 7px;
            color: var(--danger);
            font-size: 11px;
            font-weight: 700;
        }

        .password-toggle {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            position: absolute;
            top: 50%;
            right: 8px;
            color: #6366F1;
            background: transparent;
            border: 0;
            border-radius: 9px;
            cursor: pointer;
            transform: translateY(-50%);
            transition: color .2s ease, background .2s ease;
        }

        .password-toggle:hover {
            color: var(--primary-dark);
            background: var(--primary-soft);
        }

        .password-toggle:focus-visible,
        .pin-login:focus-visible,
        .submit-button:focus-visible {
            outline: 3px solid rgba(79, 70, 229, .20);
            outline-offset: 3px;
        }

        .eye-off {
            display: none;
        }

        .password-toggle.is-visible .eye-on {
            display: none;
        }

        .password-toggle.is-visible .eye-off {
            display: block;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1px 0 24px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            position: relative;
            color: #4B5563;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }

        .remember input {
            width: 18px;
            height: 18px;
            margin: 0;
            appearance: none;
            -webkit-appearance: none;
            background: #fff;
            border: 1px solid #CBD5E1;
            border-radius: 5px;
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .remember input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='m2.3 6.1 2.2 2.2 5.2-5.1' fill='none' stroke='white' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-position: center;
            background-repeat: no-repeat;
        }

        .remember input:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .13);
        }

        .secure-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #64748B;
            font-size: 10px;
            font-weight: 700;
        }

        .submit-button {
            width: 100%;
            height: 53px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            color: #fff;
            background: linear-gradient(135deg, #3B82F6 0%, #4F46E5 58%, #6366F1 100%);
            border: 0;
            border-radius: 13px;
            box-shadow: 0 12px 25px rgba(79, 70, 229, .27);
            cursor: pointer;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .01em;
            transition: transform .24s cubic-bezier(.4, 0, .2, 1), box-shadow .24s ease, background .24s ease;
        }

        .submit-button::before {
            content: "";
            width: 90px;
            height: 100%;
            position: absolute;
            top: 0;
            left: -120px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .24), transparent);
            transform: skewX(-18deg);
            transition: left .55s ease;
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #2563EB 0%, #4338CA 60%, #4F46E5 100%);
            box-shadow: 0 17px 32px rgba(79, 70, 229, .34);
            transform: translateY(-2px);
        }

        .submit-button:hover::before {
            left: calc(100% + 30px);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .submit-button svg {
            transition: transform .2s ease;
        }

        .submit-button:hover svg {
            transform: translateX(3px);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 25px 0 19px;
            color: #64748B;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #E5E7EB;
        }

        .pin-login {
            width: 100%;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            color: #374151;
            background: #FFFFFF;
            border: 1px solid #DDE2EA;
            border-radius: 13px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 3px 10px rgba(15, 23, 42, .035);
            transition: color .22s ease, border-color .22s ease, background .22s ease, transform .22s ease, box-shadow .22s ease;
        }

        .pin-login:hover {
            color: var(--primary-dark);
            background: var(--primary-soft);
            border-color: #A5B4FC;
            box-shadow: 0 9px 20px rgba(79, 70, 229, .11);
            transform: translateY(-2px);
        }

        .help-text {
            margin-top: 23px;
            color: #6B7280;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.6;
            text-align: center;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @media (max-width: 1000px) {
            .auth-page {
                padding: 22px;
            }

            .auth-shell {
                min-height: min(690px, calc(100dvh - 44px));
                grid-template-columns: minmax(0, .94fr) minmax(390px, 1.06fr);
            }

            .showcase {
                padding: 38px 34px 32px;
            }

            .showcase__copy {
                margin-top: 60px;
            }

            .showcase h1 {
                font-size: 36px;
            }

            .dashboard-preview {
                width: 100%;
            }

            .floating-note {
                right: -18px;
            }

            .login-panel {
                padding: 44px 38px;
            }
        }

        @media (max-width: 800px) {
            body {
                background:
                    radial-gradient(circle at 5% 8%, rgba(59, 130, 246, .16), transparent 20rem),
                    radial-gradient(circle at 96% 96%, rgba(139, 92, 246, .12), transparent 20rem),
                    var(--surface-soft);
            }

            .auth-page {
                display: block;
                padding: 20px;
            }

            .auth-shell {
                width: min(500px, 100%);
                min-height: calc(100dvh - 40px);
                display: block;
                margin: 0 auto;
                border-radius: 25px;
            }

            .showcase {
                display: none;
            }

            .login-panel {
                min-height: calc(100dvh - 42px);
                display: block;
                padding: 32px 34px;
            }

            .mobile-brand {
                display: block;
                margin-bottom: clamp(54px, 10vh, 88px);
            }

            .login-content {
                width: 100%;
                margin: 0 auto;
                padding: 28px;
            }
        }

        @media (max-width: 520px) {
            .auth-page {
                padding: 0;
            }

            .auth-shell {
                width: 100%;
                min-height: 100dvh;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .login-panel {
                min-height: 100dvh;
                padding: 26px 24px 32px;
            }

            .login-content {
                padding: 24px 20px;
                border-radius: 18px;
                box-shadow: 0 14px 36px rgba(30, 41, 59, .08), 0 3px 10px rgba(79, 70, 229, .04);
            }

            .mobile-brand {
                margin-bottom: clamp(48px, 9vh, 72px);
            }

            .login-heading h2 {
                font-size: 29px;
            }
        }

        @media (max-width: 360px) {
            .login-panel {
                padding-right: 18px;
                padding-left: 18px;
            }

            .secure-label {
                display: none;
            }
        }

        @media (max-height: 760px) and (min-width: 801px) {
            .auth-page {
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .auth-shell {
                min-height: 660px;
            }

            .showcase__copy {
                margin-top: 34px;
            }

            .dashboard-preview {
                margin-top: 25px;
            }

            .login-panel {
                padding-top: 32px;
                padding-bottom: 32px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
<main class="auth-page">
    <section class="auth-shell" aria-label="Login FashionPOS">
        <aside class="showcase" aria-label="Tentang FashionPOS">
            <a class="brand-mark" href="{{ route('login') }}" aria-label="FashionPOS">
                <span class="brand-mark__icon">
                    <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M8.25 8.25V6a3.75 3.75 0 0 1 7.5 0v2.25M5.35 8.25h13.3l1.1 12.25H4.25L5.35 8.25Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="brand-mark__name">Fashion<span>POS</span></span>
                    <span class="brand-mark__caption">Smart retail system</span>
                </span>
            </a>

            <div class="showcase__copy">
                <div class="eyebrow"><span class="eyebrow__dot"></span> Retail, dibuat lebih ringkas</div>
                <h1>Kendalikan toko dari satu <span>ruang kerja.</span></h1>
                <p class="showcase__lead">Transaksi, inventori, dan laporan tersusun dalam alur kerja yang cepat agar tim Anda dapat fokus melayani pelanggan.</p>

                <div class="dashboard-preview" aria-hidden="true">
                    <div class="preview-topbar">
                        <span class="preview-title">Ringkasan hari ini</span>
                        <span class="preview-status">Live</span>
                    </div>
                    <div class="metric-row">
                        <div class="metric-card">
                            <div class="metric-label">Penjualan</div>
                            <div class="metric-value">Rp8,4jt</div>
                            <div class="metric-trend">↗ 12,4%</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Transaksi</div>
                            <div class="metric-value">148</div>
                            <div class="metric-trend">↗ 8,1%</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-label">Produk aktif</div>
                            <div class="metric-value">1.284</div>
                            <div class="metric-trend">Stok aman</div>
                        </div>
                    </div>
                    <div class="chart-panel">
                        <div class="bars">
                            <span class="bar" style="--height: 30%"></span>
                            <span class="bar" style="--height: 47%"></span>
                            <span class="bar" style="--height: 38%"></span>
                            <span class="bar" style="--height: 69%"></span>
                            <span class="bar" style="--height: 55%"></span>
                            <span class="bar" style="--height: 82%"></span>
                            <span class="bar" style="--height: 72%"></span>
                            <span class="bar" style="--height: 94%"></span>
                        </div>
                        <div class="donut-wrap">
                            <span class="donut"></span>
                            <span class="donut-copy"><strong>66%</strong><span>Target tercapai</span></span>
                        </div>
                    </div>
                    <div class="floating-note">
                        <span class="floating-note__icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                <path d="m5 12.5 4 4L19 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span><strong>Stok tersinkronisasi</strong><span>Baru saja diperbarui</span></span>
                    </div>
                </div>
            </div>

            <footer class="showcase__footer">
                <span>© {{ date('Y') }} FashionPOS</span>
                <span>Sistem berjalan normal</span>
            </footer>
        </aside>

        <div class="login-panel">
            <div class="mobile-brand">
                <a class="brand-mark" href="{{ route('login') }}" aria-label="FashionPOS">
                    <span class="brand-mark__icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8.25 8.25V6a3.75 3.75 0 0 1 7.5 0v2.25M5.35 8.25h13.3l1.1 12.25H4.25L5.35 8.25Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>
                        <span class="brand-mark__name">Fashion<span>POS</span></span>
                        <span class="brand-mark__caption">Smart retail system</span>
                    </span>
                </a>
            </div>

            <div class="login-content">
                <header class="login-heading">
                    <div class="login-kicker">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3 4.5 6v5.25c0 4.63 3.2 8.95 7.5 10.05 4.3-1.1 7.5-5.42 7.5-10.05V6L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Portal aman
                    </div>
                    <h2>Selamat datang kembali.</h2>
                    <p>Masukkan detail akun Anda untuk melanjutkan ke dashboard.</p>
                </header>

                @if(session('success'))
                    <div class="alert alert--success" role="status">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="m8.5 12 2.25 2.25L15.5 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert--error" role="alert">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 7.8v4.8M12 16.2h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf

                    <div class="field">
                        <label class="field__label" for="email">Alamat email <span class="field__required" aria-hidden="true">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-wrap__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6.75h16v10.5H4V6.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="m4.5 7.25 7.5 5.5 7.5-5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input
                                class="field__input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                autocomplete="email"
                                inputmode="email"
                                autofocus
                                required
                                @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                        </div>
                        @error('email')
                            <div class="field__error" id="email-error" role="alert">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <circle cx="6" cy="6" r="5" stroke="currentColor"/>
                                    <path d="M6 3.3v3.1M6 8.6h.01" stroke="currentColor" stroke-linecap="round"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">Password <span class="field__required" aria-hidden="true">*</span></label>
                        <div class="input-wrap">
                            <svg class="input-wrap__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6.5 10.5h11v9h-11v-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M8.75 10.5V8a3.25 3.25 0 1 1 6.5 0v2.5M12 14v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            <input
                                class="field__input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                                @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
                            <button class="password-toggle" type="button" id="passwordToggle" aria-label="Tampilkan password" aria-pressed="false">
                                <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.75 12s3.25-5.25 9.25-5.25S21.25 12 21.25 12 18 17.25 12 17.25 2.75 12 2.75 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="2.25" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                                <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m4 4 16 16M9.2 7.2A8.4 8.4 0 0 1 12 6.75c6 0 9.25 5.25 9.25 5.25a15.5 15.5 0 0 1-2.1 2.65M14.4 16.9c-.75.23-1.55.35-2.4.35C6 17.25 2.75 12 2.75 12a15 15 0 0 1 3.1-3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="field__error" id="password-error" role="alert">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <circle cx="6" cy="6" r="5" stroke="currentColor"/>
                                    <path d="M6 3.3v3.1M6 8.6h.01" stroke="currentColor" stroke-linecap="round"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember" for="remember">
                            <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Ingat saya</span>
                        </label>
                        <span class="secure-label">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 10.5h10v9H7v-9ZM9 10.5V8a3 3 0 0 1 6 0v2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Koneksi aman
                        </span>
                    </div>

                    <button type="submit" class="submit-button">
                        <span>Masuk ke dashboard</span>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>

                <div class="divider">atau akses cepat</div>

                <a href="{{ route('login.pin.page') }}" class="pin-login">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="4" y="4" width="6" height="6" rx="1.2" stroke="currentColor" stroke-width="1.6"/>
                        <rect x="14" y="4" width="6" height="6" rx="1.2" stroke="currentColor" stroke-width="1.6"/>
                        <rect x="4" y="14" width="6" height="6" rx="1.2" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M14 15.5h2M19 15.5h1M14 19.5h1M18 19.5h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Login dengan PIN kasir
                </a>

                <p class="help-text">Kesulitan mengakses akun? Hubungi administrator toko Anda.</p>
            </div>
        </div>
    </section>
</main>

<script>
    (() => {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');

        passwordToggle.addEventListener('click', () => {
            const shouldShow = passwordInput.type === 'password';

            passwordInput.type = shouldShow ? 'text' : 'password';
            passwordToggle.classList.toggle('is-visible', shouldShow);
            passwordToggle.setAttribute('aria-pressed', String(shouldShow));
            passwordToggle.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
            passwordInput.focus({ preventScroll: true });
        });
    })();
</script>
</body>
</html>
