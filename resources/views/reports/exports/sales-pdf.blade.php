<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 18px; color: #4F46E5; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background-color: #f1f5f9; color: #475569; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .table tr.total-row { font-weight: bold; background-color: #f8fafc; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Kasir</th>
                <th class="text-right">Qty Item</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Pajak</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['transactions'] ?? [] as $trx)
            <tr>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-weight: bold;">{{ $trx->invoice_number }}</td>
                <td>{{ $trx->cashierSession?->user?->name ?? '-' }}</td>
                <td class="text-right">{{ $trx->items->sum('quantity') }}</td>
                <td class="text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->discount_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->tax_amount, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
            
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td class="text-right">{{ collect($report['transactions'] ?? [])->flatMap->items->sum('quantity') }}</td>
                <td class="text-right">Rp {{ number_format(collect($report['transactions'] ?? [])->sum('subtotal'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(collect($report['transactions'] ?? [])->sum('discount_amount'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(collect($report['transactions'] ?? [])->sum('tax_amount'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($report['total_revenue'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
