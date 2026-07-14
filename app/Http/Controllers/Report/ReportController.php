<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\StockService;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private StockService $stockService
    ) {}

    public function salesIndex(Request $request)
    {
        $startDate = $request->start_date ?? today()->format('Y-m-d');
        $endDate = $request->end_date ?? today()->format('Y-m-d');
        $userId = $request->user_id;

        $report = $this->reportService->getSalesReport($startDate, $endDate, $userId);
        $cashiers = User::whereHas('role', fn($q) => $q->whereIn('name', ['kasir', 'supervisor', 'admin']))->get();
        $chartData = $this->stockService->getSalesChart('week');

        return view('reports.sales', compact('report', 'startDate', 'endDate', 'cashiers', 'chartData'));
    }

    public function financialIndex(Request $request)
    {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $financial = $this->reportService->getProfitLossReport($month, $year);
        $cashierReport = $this->reportService->getCashierReport(
            "{$year}-{$month}-01",
            date('Y-m-t', strtotime("{$year}-{$month}-01"))
        );

        $topProducts = $this->stockService->getTopSellingProducts('month', 10);

        return view('reports.financial', compact('financial', 'cashierReport', 'topProducts', 'month', 'year'));
    }

    public function inventoryIndex(Request $request)
    {
        $lowStock = $this->stockService->getLowStockProducts();
        $stockValue = $this->stockService->getStockValue();
        $topProducts = $this->stockService->getTopSellingProducts('month', 10);
        $slowMoving = $this->getSlowMovingProducts();

        return view('reports.inventory', compact('lowStock', 'stockValue', 'topProducts', 'slowMoving'));
    }

    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->start_date ?? today()->format('Y-m-d');
        $endDate = $request->end_date ?? today()->format('Y-m-d');

        $report = $this->reportService->getSalesReport($startDate, $endDate);

        $pdf = Pdf::loadView('reports.exports.sales-pdf', compact('report', 'startDate', 'endDate'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-penjualan-' . $startDate . '-sd-' . $endDate . '.pdf');
    }

    public function exportSalesExcel(Request $request)
    {
        $startDate = $request->start_date ?? today()->format('Y-m-d');
        $endDate = $request->end_date ?? today()->format('Y-m-d');

        $report = $this->reportService->getSalesReport($startDate, $endDate);

        $filename = 'laporan-penjualan-' . $startDate . '-sd-' . $endDate . '.csv';
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = ['Tanggal', 'No Invoice', 'Kasir', 'Total Item', 'Subtotal', 'Diskon', 'Pajak', 'Grand Total'];

        $callback = function() use($report, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($report['transactions'] ?? [] as $trx) {
                fputcsv($file, [
                    $trx->created_at->format('Y-m-d H:i:s'),
                    $trx->invoice_number,
                    $trx->cashierSession?->user?->name ?? '-',
                    $trx->items->sum('quantity'),
                    $trx->subtotal,
                    $trx->discount_amount,
                    $trx->tax_amount,
                    $trx->grand_total
                ]);
            }
            
            // Summary row
            fputcsv($file, []);
            fputcsv($file, ['TOTAL PENDAPATAN:', '', '', '', '', '', '', $report['total_revenue'] ?? 0]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportFinancialPdf(Request $request)
    {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $report = $this->reportService->getProfitLossReport($month, $year);

        $pdf = Pdf::loadView('reports.exports.financial-pdf', compact('report'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $year . '-' . $month . '.pdf');
    }

    private function getSlowMovingProducts(int $days = 30)
    {
        $startDate = now()->subDays($days);

        return \App\Models\Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->whereDoesntHave('variants.stockMovements', function ($q) use ($startDate) {
                $q->where('type', 'out')->where('created_at', '>=', $startDate);
            })
            ->take(10)
            ->get();
    }

    public function cashierReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();
        $userId   = $request->user_id;

        $query = \App\Models\CashierSession::with(['user', 'transactions'])
            ->whereBetween('opened_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest('opened_at');

        $sessions = $query->get();

        // Group by kasir
        $kasirData = $sessions->groupBy('user_id')->map(function ($sessionGroup) {
            $user = $sessionGroup->first()->user;
            $totalSales = $sessionGroup->sum('total_sales');
            $totalTrx   = $sessionGroup->sum('total_transactions');
            $totalDiff  = $sessionGroup->sum('difference');
            return [
                'user'        => $user,
                'sessions'    => $sessionGroup,
                'total_sales' => $totalSales,
                'total_trx'   => $totalTrx,
                'total_diff'  => $totalDiff,
                'avg_per_trx' => $totalTrx > 0 ? round($totalSales / $totalTrx) : 0,
            ];
        });

        $users = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'kasir'))->get();

        return view('reports.cashier', compact('kasirData', 'sessions', 'users', 'dateFrom', 'dateTo'));
    }

    public function discountReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $transactions = \App\Models\Transaction::with(['items', 'cashierSession.user'])
            ->whereIn('status', ['completed', 'partial_return'])
            ->where('discount_amount', '>', 0)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = \App\Models\Transaction::whereIn('status', ['completed', 'partial_return'])
            ->where('discount_amount', '>', 0)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->selectRaw('SUM(discount_amount) as total_discount, COUNT(*) as total_trx, AVG(discount_percent) as avg_discount_pct')
            ->first();

        return view('reports.discount', compact('transactions', 'summary', 'dateFrom', 'dateTo'));
    }

    // FIN-06: Laporan per Kategori
    public function categoryReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $data = \App\Models\TransactionItem::with(['productVariant.product.category'])
            ->whereHas('transaction', fn($q) => $q
                ->whereIn('status', ['completed', 'partial_return'])
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']))
            ->get()
            ->groupBy(fn($item) => $item->productVariant?->product?->category?->name ?? 'Tanpa Kategori')
            ->map(fn($items) => [
                'total_qty'    => $items->sum('quantity'),
                'total_sales'  => $items->sum('subtotal'),
                'total_profit' => $items->sum(fn($i) => ($i->unit_price - $i->buy_price) * $i->quantity),
            ])
            ->sortByDesc('total_sales');

        return view('reports.category', compact('data', 'dateFrom', 'dateTo'));
    }

    // FIN-07: Laporan per Metode Pembayaran
    public function paymentMethodReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $data = \App\Models\TransactionPayment::with('paymentMethod')
            ->whereHas('transaction', fn($q) => $q
                ->whereIn('status', ['completed', 'partial_return'])
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']))
            ->get()
            ->groupBy(fn($p) => $p->paymentMethod?->name ?? 'Unknown')
            ->map(fn($payments) => [
                'total_amount' => $payments->sum('amount'),
                'total_count'  => $payments->count(),
            ])
            ->sortByDesc('total_amount');

        return view('reports.payment-method', compact('data', 'dateFrom', 'dateTo'));
    }

    // FIN-08: Laporan Retur & Void
    public function returnsVoidsReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? now()->toDateString();

        $returns = \App\Models\ReturnTransaction::with(['transaction', 'requestedBy'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()->get();

        $voids = \App\Models\Transaction::where('status', 'voided')
            ->with(['cashierSession.user'])
            ->whereBetween('voided_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()->get();

        return view('reports.returns-voids', compact('returns', 'voids', 'dateFrom', 'dateTo'));
    }

    // ANL-05: Analisis Jam Sibuk
    public function busyHoursReport(Request $request)
    {
        $days = $request->days ?? 30;

        $hourly = \App\Models\Transaction::whereIn('status', ['completed', 'partial_return'])
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as trx_count, SUM(grand_total) as total_sales')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Fill missing hours with zeros
        $heatmap = collect(range(0, 23))->map(fn($h) => [
            'hour'        => $h,
            'label'       => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
            'trx_count'   => $hourly->get($h)?->trx_count ?? 0,
            'total_sales' => $hourly->get($h)?->total_sales ?? 0,
        ]);

        return view('reports.busy-hours', compact('heatmap', 'days'));
    }
}


