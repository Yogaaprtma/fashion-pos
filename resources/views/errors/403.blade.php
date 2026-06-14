<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | FashionPOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .container {
            text-align: center;
            max-width: 500px;
        }
        .icon-wrap {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            margin: 0 auto 28px;
            box-shadow: 0 8px 24px rgba(239,68,68,.2);
        }
        h1 { font-size: 28px; font-weight: 700; color: #0F172A; margin-bottom: 8px; }
        .sub  { font-size: 15px; color: #64748B; margin-bottom: 28px; line-height: 1.6; }
        .code { display: inline-block; background: #f1f5f9; color: #94a3b8; font-size: 72px; font-weight: 800; letter-spacing: -4px; margin-bottom: 20px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #2563EB;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background .2s;
            margin: 4px;
        }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary { background: #f1f5f9; color: #374151; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrap">🚫</div>
        <div class="code">403</div>
        <h1>Akses Ditolak</h1>
        <p class="sub">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Hubungi Administrator jika Anda merasa ini adalah kesalahan.
        </p>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">← Kembali</a>
            <a href="{{ route('dashboard') }}" class="btn">🏠 Ke Dashboard</a>
        </div>
    </div>
</body>
</html>
