<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; color: #4F46E5; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table td { padding: 8px 12px; border-bottom: 1px solid #ddd; }
        .table tr.section-header { background-color: #f1f5f9; font-weight: bold; }
        .table tr.section-header td { font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; color: #475569; }
        .table tr.total-row { font-weight: bold; background-color: #f8fafc; }
        .table tr.grand-total-row { font-weight: bold; background-color: #4F46E5; color: white; }
        .table tr.grand-total-row td { font-size: 14px; border: none; }
        .text-right { text-align: right; }
        .text-danger { color: #ef4444; }
        .text-success { color: #10b981; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI (P&L)</h1>
        <p>Periode: {{ $report['period'] ?? '-' }}</p>
    </div>

    <table class="table">
        <tbody>
            <tr class="section-header">
                <td colspan="2">PENDAPATAN (REVENUE)</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Penjualan Kotor</td>
                <td class="text-right">Rp {{ number_format($report['gross_sales'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;" class="text-danger">Retur Penjualan</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['returns'] ?? 0, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;" class="text-danger">Diskon Diberikan</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['discounts'] ?? 0, 0, ',', '.') }})</td>
            </tr>
            <tr class="total-row" style="color: #065f46; background-color: #f0fdf4;">
                <td>Penjualan Bersih (Net Sales)</td>
                <td class="text-right">Rp {{ number_format($report['net_sales'] ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr class="section-header">
                <td colspan="2">HARGA POKOK PENJUALAN (COGS)</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Biaya Pembelian Barang Terjual</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['cogs'] ?? 0, 0, ',', '.') }})</td>
            </tr>

            <tr class="total-row" style="color: #065f46; background-color: #ecfdf5;">
                <td style="font-size: 14px;">LABA KOTOR (GROSS PROFIT)</td>
                <td class="text-right" style="font-size: 14px;">Rp {{ number_format($report['gross_profit'] ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr class="section-header">
                <td colspan="2">BEBAN OPERASIONAL (OPEX)</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Depresiasi Aset</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['depreciation'] ?? 0, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Selisih Opname (Shrinkage)</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['shrinkage'] ?? 0, 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Pengeluaran & Kas Kecil</td>
                <td class="text-right text-danger">(Rp {{ number_format($report['expenses'] ?? 0, 0, ',', '.') }})</td>
            </tr>

            <tr class="grand-total-row">
                <td>LABA BERSIH (NET PROFIT)</td>
                <td class="text-right">Rp {{ number_format($report['net_profit'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
