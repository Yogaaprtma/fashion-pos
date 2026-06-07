<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CashierSession;
use App\Models\StoreSetting;
use App\Services\StockService;
use App\Services\TransactionService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private StockService $stockService,
        private TransactionService $transactionService
    ) {}

    public function index()
    {
        $today = today();
        $yesterday = today()->subDay();
        $thisMonth = now()->startOfMonth();

        // Today's stats
        $todayRevenue = Transaction::whereDate('created_at', $today)->where('status', 'completed')->sum('grand_total');
        $todayTransactions = Transaction::whereDate('created_at', $today)->where('status', 'completed')->count();
        $yesterdayRevenue = Transaction::whereDate('created_at', $yesterday)->where('status', 'completed')->sum('grand_total');

        $revenueGrowth = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : 0;

        // Monthly stats
        $monthRevenue = Transaction::where('created_at', '>=', $thisMonth)->where('status', 'completed')->sum('grand_total');
        $monthTransactions = Transaction::where('created_at', '>=', $thisMonth)->where('status', 'completed')->count();

        // Gross Profit today
        $todayTransactionsData = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->with('items')
            ->get();
        $todayProfit = $todayTransactionsData->sum(fn($t) => $t->getGrossProfit());
        $profitMargin = $todayRevenue > 0 ? ($todayProfit / $todayRevenue) * 100 : 0;

        // Active cashier sessions
        $activeSessions = CashierSession::where('status', 'open')->with('user')->count();

        // Low stock products
        $lowStockCount = Product::with('variants')
            ->get()
            ->filter(fn($p) => $p->isLowStock())
            ->count();

        // Total inventory value
        $inventoryValue = $this->stockService->getStockValue();

        // Top products (this month)
        $topProducts = $this->stockService->getTopSellingProducts('month', 5);

        // Chart data
        $chartData = $this->stockService->getSalesChart('week');

        // Recent transactions
        $recentTransactions = Transaction::with(['cashierSession.user', 'items'])
            ->where('status', 'completed')
            ->latest()
            ->take(8)
            ->get();

        // Store info
        $storeName = StoreSetting::get('store_name', 'FashionPOS');

        return view('dashboard.index', compact(
            'todayRevenue', 'todayTransactions', 'revenueGrowth',
            'monthRevenue', 'monthTransactions',
            'todayProfit', 'profitMargin',
            'activeSessions', 'lowStockCount', 'inventoryValue',
            'topProducts', 'chartData', 'recentTransactions', 'storeName'
        ));
    }

    public function getChartData(string $period = 'week')
    {
        $data = (new StockService())->getSalesChart($period);
        return response()->json($data);
    }
}
