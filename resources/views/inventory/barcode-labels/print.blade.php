<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barcode - {{ $storeName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }

        /* Print Controls (hidden when printing) */
        .print-controls {
            background: #1e1e2e;
            color: white;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .print-controls h2 { font-size: 16px; font-weight: 700; }
        .print-controls .meta { font-size: 12px; color: #aaa; margin-top: 2px; }
        .btn-print {
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-close {
            background: transparent;
            color: #aaa;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 10px 18px;
            font-size: 13px;
            cursor: pointer;
        }

        /* Label Sheet Grid */
        .label-sheet {
            padding: 8mm;
            display: flex;
            flex-wrap: wrap;
            gap: 3mm;
            background: white;
            min-height: 100vh;
        }

        /* Individual Labels */
        @php
            [$lw, $lh] = explode('x', $labelSize);
        @endphp

        .label {
            width: {{ $lw }}mm;
            height: {{ $lh }}mm;
            border: 0.5pt solid #999;
            border-radius: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5mm;
            overflow: hidden;
            background: white;
            position: relative;
        }

        .label-store {
            font-size: 6pt;
            font-weight: 700;
            color: #333;
            text-align: center;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .label-name {
            font-size: 7pt;
            font-weight: 700;
            color: #111;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 1mm;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            max-width: 100%;
        }

        .label-variant-info {
            font-size: 6pt;
            color: #555;
            text-align: center;
            margin-bottom: 1mm;
        }

        /* Barcode (uses CSS to draw rough barcode lines - or IMG from API) */
        .label-barcode {
            text-align: center;
            margin-bottom: 1mm;
            width: 100%;
        }

        .label-barcode img {
            max-width: 100%;
            height: {{ (int)$lh > 25 ? '12mm' : '8mm' }};
            object-fit: contain;
        }

        .label-sku {
            font-size: 5.5pt;
            color: #777;
            text-align: center;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5pt;
        }

        .label-price {
            font-size: 9pt;
            font-weight: 900;
            color: #111;
            text-align: center;
            margin-top: 1mm;
        }

        /* Print Media */
        @media print {
            .print-controls { display: none !important; }
            body { background: white; }
            .label-sheet { padding: 4mm; gap: 2mm; }
        }
    </style>
</head>
<body>

{{-- Screen-only print controls --}}
<div class="print-controls">
    <div>
        <h2>🏷️ Label Barcode Siap Cetak</h2>
        <div class="meta">{{ count($labels) }} label · Ukuran {{ $labelSize }}mm · {{ $storeName }}</div>
    </div>
    <div style="display:flex;gap:10px;">
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Sekarang</button>
    </div>
</div>

{{-- Label Sheet --}}
<div class="label-sheet">
    @foreach($labels as $variant)
    <div class="label">

        {{-- Store Name --}}
        <div class="label-store">{{ $storeName }}</div>

        {{-- Product Name --}}
        <div class="label-name">{{ $variant->product->name }}</div>

        {{-- Variant Info --}}
        @if($showVariant)
        <div class="label-variant-info">{{ $variant->variant_label }}</div>
        @endif

        {{-- Barcode --}}
        @if($showBarcode)
        <div class="label-barcode">
            {{-- Uses barcode.tec-it.com free barcode API --}}
            <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($variant->sku) }}&code=Code128&dpi=150&quietzone=0&qr=0&ln=0" alt="{{ $variant->sku }}">
            <div class="label-sku">{{ $variant->sku }}</div>
        </div>
        @else
        <div class="label-sku" style="margin-bottom:2mm;">{{ $variant->sku }}</div>
        @endif

        {{-- Price --}}
        @if($showPrice)
        <div class="label-price">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</div>
        @endif

    </div>
    @endforeach
</div>

</body>
</html>
