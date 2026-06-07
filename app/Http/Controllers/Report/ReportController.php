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

        $report = $this->reportService->getProfitLossReport($month, $year);
        $cashierReport = $this->reportService->getCashierReport(
            "{$year}-{$month}-01",
            date('Y-m-t', strtotime("{$year}-{$month}-01"))
        );

        $topProducts = $this->stockService->getTopSellingProducts('month', 10);

        return view('reports.financial', compact('report', 'cashierReport', 'topProducts', 'month', 'year'));
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
}
