<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .center { text-align: center; }
        .right { text-align: right; }
        .flex-between { width: 100%; display: table; }
        .flex-left { display: table-cell; text-align: left; }
        .flex-right { display: table-cell; text-align: right; }
        hr { border: none; border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
    </style>
</head>
<body>
    <div class="center" style="margin-bottom: 8px;">
        <strong style="font-size: 14px;">{{ $storeSettings['store_name'] ?? 'FashionPOS' }}</strong><br>
        {{ $storeSettings['store_address'] ?? 'Alamat Toko' }}
    </div>
    <hr>
    <div>
        INV: {{ $transaction->invoice_number }}<br>
        Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
        Ksr: {{ $transaction->cashierSession?->user?->name ?? 'Kasir' }}
    </div>
    <hr>
    
    <table>
        @foreach($transaction->items as $item)
        <tr>
            <td colspan="3"><strong>{{ $item->product_name }}</strong> ({{ $item->variant_info }})</td>
        </tr>
        <tr>
            <td style="padding-left:10px;">{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</td>
            <td class="right" colspan="2">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    <hr>
    
    <div class="flex-between">
        <div class="flex-left">Subtotal:</div>
        <div class="flex-right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</div>
    </div>
    @if($transaction->discount_amount > 0)
    <div class="flex-between">
        <div class="flex-left">Diskon:</div>
        <div class="flex-right">-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</div>
    </div>
    @endif
    <div class="flex-between" style="font-weight: bold; margin-top:4px; font-size:12px;">
        <div class="flex-left">TOTAL:</div>
        <div class="flex-right">{{ number_format($transaction->grand_total, 0, ',', '.') }}</div>
    </div>
    <hr>
    @foreach($transaction->payments as $pay)
    <div class="flex-between">
        <div class="flex-left">{{ $pay->paymentMethod?->name ?? 'Bayar' }}:</div>
        <div class="flex-right">{{ number_format($pay->amount, 0, ',', '.') }}</div>
    </div>
    @endforeach
    <div class="flex-between">
        <div class="flex-left">Kembali:</div>
        <div class="flex-right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
    </div>
    
    <div class="center" style="margin-top: 15px;">
        Terima Kasih
    </div>
</body>
</html>
