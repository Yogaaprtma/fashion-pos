<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #f0f0f0;
            margin: 0;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .receipt {
            background: #fff;
            width: 300px;
            padding: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .flex-between { display: flex; justify-content: space-between; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        .item-row { margin-bottom: 4px; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; padding-left: 8px; font-size: 11px; }
        
        @media print {
            body { background: #fff; padding: 0; display: block; }
            .receipt { box-shadow: none; width: 100%; max-width: 300px; margin: 0 auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt">
    <div class="center" style="margin-bottom: 12px;">
        <h2 style="margin: 0; font-size: 18px;">{{ $storeSettings['store_name'] ?? 'FashionPOS' }}</h2>
        <div style="font-size: 11px;">{{ $storeSettings['store_address'] ?? 'Alamat Toko' }}</div>
        <div style="font-size: 11px;">Telp: {{ $storeSettings['store_phone'] ?? '-' }}</div>
    </div>
    
    <div class="center" style="font-size: 11px; margin-bottom: 8px;">
        {{ $receiptSettings['receipt_header'] ?? 'Selamat Datang' }}
    </div>
    <hr>
    
    <div style="font-size: 11px; margin-bottom: 8px;">
        <div class="flex-between">
            <span>INV: {{ $transaction->invoice_number }}</span>
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex-between">
            <span>Kasir: {{ $transaction->cashierSession?->user?->name ?? 'Kasir' }}</span>
        </div>
    </div>
    <hr>
    
    <!-- Items -->
    <div style="margin-bottom: 8px;">
        @foreach($transaction->items as $item)
        <div class="item-row">
            <div class="item-name">{{ $item->product_name }} ({{ $item->variant_info }})</div>
            <div class="item-detail">
                <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($item->discount_amount > 0)
            <div class="item-detail" style="justify-content: flex-end;">
                <span>Disc: -{{ number_format($item->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <hr>
    
    <!-- Totals -->
    <div style="font-size: 11px; margin-bottom: 8px;">
        <div class="flex-between">
            <span>Subtotal:</span>
            <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($transaction->discount_amount > 0)
        <div class="flex-between">
            <span>Total Diskon:</span>
            <span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($transaction->tax_amount > 0)
        <div class="flex-between">
            <span>PPN:</span>
            <span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>
    
    <hr>
    <div class="flex-between" style="font-size: 14px; font-weight: bold; margin-bottom: 8px;">
        <span>TOTAL:</span>
        <span>{{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
    </div>
    <hr>
    
    <div style="font-size: 11px; margin-bottom: 12px;">
        @foreach($transaction->payments as $pay)
        <div class="flex-between">
            <span>{{ $pay->paymentMethod?->name ?? 'Bayar' }}:</span>
            <span>{{ number_format($pay->amount, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="flex-between" style="margin-top: 4px;">
            <span>Kembali:</span>
            <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="center" style="font-size: 11px; margin-top: 16px;">
        {{ $receiptSettings['receipt_footer'] ?? 'Terima Kasih atas kunjungan Anda' }}
    </div>

    <!-- Actions -->
    <div class="no-print center" style="margin-top: 24px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">🖨️ Cetak (Thermal)</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">Tutup</button>
    </div>
</div>

<script>
    // Opsional: Langsung muncul dialog print jika param auto_print ada
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('auto_print')) {
            window.print();
        }
    }
</script>
</body>
</html>
