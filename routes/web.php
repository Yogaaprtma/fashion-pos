<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POS\CashierController;
use App\Http\Controllers\POS\TransactionController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

// ============================================================
// AUTH ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/pin-login', [LoginController::class, 'showPinLogin'])->name('login.pin.page');
    Route::post('/pin-login', [LoginController::class, 'pinLogin'])->name('login.pin');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// PROTECTED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {

    // Redirect root to dashboard
    Route::redirect('/', '/dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart/{period}', [DashboardController::class, 'getChartData'])->name('dashboard.chart');

    // ============================================================
    // POS ROUTES
    // ============================================================
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('index');

        // Session management
        Route::get('/session/open', [CashierController::class, 'openSession'])->name('session.open');
        Route::post('/session/start', [CashierController::class, 'startSession'])->name('session.start');
        Route::post('/session/close', [CashierController::class, 'closeSession'])->name('session.close');
        Route::get('/session/{session}/report', [CashierController::class, 'sessionReport'])->name('session.report');

        // Product search API
        Route::get('/search-products', [CashierController::class, 'searchProducts'])->name('products.search');
        Route::get('/barcode', [CashierController::class, 'getByBarcode'])->name('barcode');

        // Transactions
        Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
        Route::get('/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
        Route::post('/transaction/{transaction}/void', [TransactionController::class, 'void'])->name('transaction.void');
        Route::post('/transaction/{transaction}/return', [TransactionController::class, 'processReturn'])->name('transaction.return');
        Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
        Route::get('/transaction/{transaction}/receipt-pdf', [TransactionController::class, 'receiptPdf'])->name('transaction.receipt-pdf');
        Route::get('/history', [TransactionController::class, 'history'])->name('history');
    });

    // ============================================================
    // INVENTORY ROUTES
    // ============================================================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/barcode-generator', [ProductController::class, 'barcodeGenerator'])->name('barcode-generator');
        Route::resource('products', ProductController::class);

        // Categories
        Route::get('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Stock
        Route::get('/stock', [\App\Http\Controllers\Inventory\StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/movements', [\App\Http\Controllers\Inventory\StockController::class, 'movements'])->name('stock.movements');
        Route::get('/stock/low', [\App\Http\Controllers\Inventory\StockController::class, 'lowStock'])->name('stock.low');
        Route::post('/stock/adjust', [\App\Http\Controllers\Inventory\StockController::class, 'adjust'])->name('stock.adjust');

        // Stock Opname
        Route::get('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'index'])->name('opname.index');
        Route::post('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'store'])->name('opname.store');
        Route::get('/opname/{opname}', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'show'])->name('opname.show');
        Route::post('/opname/{opname}/approve', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'approve'])->name('opname.approve');
    });

    // ============================================================
    // PURCHASE ROUTES
    // ============================================================
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\Purchase\SupplierController::class);
        Route::resource('orders', \App\Http\Controllers\Purchase\PurchaseOrderController::class);
        Route::post('/orders/{order}/receive', [\App\Http\Controllers\Purchase\PurchaseOrderController::class, 'receive'])->name('orders.receive');
    });

    // ============================================================
    // REPORTS ROUTES
    // ============================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesIndex'])->name('sales');
        Route::get('/sales/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('sales.export-pdf');
        Route::get('/sales/export-excel', [ReportController::class, 'exportSalesExcel'])->name('sales.export-excel');
        Route::get('/financial', [ReportController::class, 'financialIndex'])->name('financial');
        Route::get('/financial/export-pdf', [ReportController::class, 'exportFinancialPdf'])->name('financial.export-pdf');
        Route::get('/inventory', [ReportController::class, 'inventoryIndex'])->name('inventory');
    });

    // ============================================================
    // ASSETS ROUTES
    // ============================================================
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::resource('/', \App\Http\Controllers\AssetController::class)->parameters(['' => 'asset']);
        Route::resource('categories', \App\Http\Controllers\AssetCategoryController::class);
    });

    // ============================================================
    // USER MANAGEMENT
    // ============================================================
    Route::resource('users', UserController::class)->except(['show']);

    // ============================================================
    // SETTINGS
    // ============================================================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/store', [SettingController::class, 'updateStore'])->name('store');
        Route::put('/tax', [SettingController::class, 'updateTax'])->name('tax');
        Route::put('/receipt', [SettingController::class, 'updateReceipt'])->name('receipt');
        Route::put('/payment-methods', [SettingController::class, 'updatePaymentMethods'])->name('payment-methods');
    });

    // ============================================================
    // AUDIT LOG
    // ============================================================
    Route::get('/audit-logs', function() {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(30);
        return view('audit-logs.index', compact('logs'));
    })->name('audit-logs.index');
});
