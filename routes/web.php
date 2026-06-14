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
use App\Http\Controllers\ProfileController;

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
    // PROFIL PENGGUNA (Semua role bisa akses)
    // ============================================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    });

    // ============================================================
    // POS ROUTES
    // ============================================================
    Route::prefix('pos')->name('pos.')->middleware('permission:pos.access')->group(function () {
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
        Route::post('/transaction/{transaction}/void', [TransactionController::class, 'void'])->middleware('permission:pos.void')->name('transaction.void');
        Route::post('/transaction/{transaction}/return', [TransactionController::class, 'processReturn'])->middleware('permission:pos.return')->name('transaction.return');
        Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
        Route::get('/transaction/{transaction}/receipt-pdf', [TransactionController::class, 'receiptPdf'])->name('transaction.receipt-pdf');
        Route::get('/history', [TransactionController::class, 'history'])->name('history');

        // Hold & Recall
        Route::post('/transaction/{transaction}/hold', [TransactionController::class, 'hold'])->name('transaction.hold');
        Route::post('/transaction/{transaction}/recall', [TransactionController::class, 'recall'])->name('transaction.recall');
        Route::get('/held', [TransactionController::class, 'heldList'])->name('held');
    });

    // ============================================================
    // INVENTORY ROUTES
    // ============================================================
    Route::prefix('inventory')->name('inventory.')->middleware('permission:inventory.view')->group(function () {
        Route::get('/barcode-generator', [ProductController::class, 'barcodeGenerator'])->name('barcode-generator');
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/upload-image', [ProductController::class, 'uploadImage'])->name('products.upload-image');
        Route::delete('/products/{product}/delete-image/{image}', [ProductController::class, 'deleteImage'])->name('products.delete-image');

        // Categories
        Route::get('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Stock
        Route::get('/stock', [\App\Http\Controllers\Inventory\StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/movements', [\App\Http\Controllers\Inventory\StockController::class, 'movements'])->name('stock.movements');
        Route::get('/stock/low', [\App\Http\Controllers\Inventory\StockController::class, 'lowStock'])->name('stock.low');
        Route::post('/stock/adjust', [\App\Http\Controllers\Inventory\StockController::class, 'adjust'])->middleware('permission:inventory.manage')->name('stock.adjust');

        // Stock Opname
        Route::get('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'index'])->name('opname.index');
        Route::post('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'store'])->middleware('permission:inventory.opname')->name('opname.store');
        Route::get('/opname/{opname}', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'show'])->name('opname.show');
        Route::post('/opname/{opname}/approve', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'approve'])->middleware('permission:inventory.opname')->name('opname.approve');
    });

    // ============================================================
    // PURCHASE ROUTES
    // ============================================================
    Route::prefix('purchase')->name('purchase.')->middleware('permission:purchase.view')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\Purchase\SupplierController::class);
        Route::resource('orders', \App\Http\Controllers\Purchase\PurchaseOrderController::class);
        Route::post('/orders/{order}/receive', [\App\Http\Controllers\Purchase\PurchaseOrderController::class, 'receive'])->name('orders.receive');
    });

    // ============================================================
    // RETUR ROUTES (Supervisor ke atas)
    // ============================================================
    Route::prefix('returns')->name('returns.')->middleware('permission:pos.return')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReturnController::class, 'index'])->name('index');
        Route::get('/{return}', [\App\Http\Controllers\ReturnController::class, 'show'])->name('show');
        Route::post('/{return}/approve', [\App\Http\Controllers\ReturnController::class, 'approve'])->name('approve');
        Route::post('/{return}/reject', [\App\Http\Controllers\ReturnController::class, 'reject'])->name('reject');
    });

    // ============================================================
    // REPORTS ROUTES
    // ============================================================
    Route::prefix('reports')->name('reports.')->middleware('permission:report.sales')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesIndex'])->name('sales');
        Route::get('/sales/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('sales.export-pdf');
        Route::get('/sales/export-excel', [ReportController::class, 'exportSalesExcel'])->name('sales.export-excel');
        Route::get('/cashier', [ReportController::class, 'cashierReport'])->name('cashier');
        Route::get('/discount', [ReportController::class, 'discountReport'])->name('discount');
        Route::get('/financial', [ReportController::class, 'financialIndex'])->middleware('permission:report.financial')->name('financial');
        Route::get('/financial/export-pdf', [ReportController::class, 'exportFinancialPdf'])->middleware('permission:report.financial')->name('financial.export-pdf');
        Route::get('/inventory', [ReportController::class, 'inventoryIndex'])->name('inventory');
    });

    // ============================================================
    // ASSETS ROUTES
    // ============================================================
    Route::prefix('assets')->name('assets.')->middleware('permission:asset.view')->group(function () {
        Route::resource('/', \App\Http\Controllers\AssetController::class)->parameters(['' => 'asset']);
        Route::resource('categories', \App\Http\Controllers\AssetCategoryController::class);
    });

    // ============================================================
    // USER MANAGEMENT (Admin only)
    // ============================================================
    Route::resource('users', UserController::class)->except(['show'])->middleware('permission:user.manage');

    // ============================================================
    // SETTINGS (Admin only)
    // ============================================================
    Route::prefix('settings')->name('settings.')->middleware('permission:setting.manage')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/store', [SettingController::class, 'updateStore'])->name('store');
        Route::put('/tax', [SettingController::class, 'updateTax'])->name('tax');
        Route::put('/receipt', [SettingController::class, 'updateReceipt'])->name('receipt');
        Route::put('/payment-methods', [SettingController::class, 'updatePaymentMethods'])->name('payment-methods');
        Route::post('/backup', [SettingController::class, 'backup'])->name('backup');
    });

    // ============================================================
    // AUDIT LOG
    // ============================================================
    Route::get('/audit-logs', function() {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(30);
        return view('audit-logs.index', compact('logs'));
    })->name('audit-logs.index')->middleware('permission:user.manage');

    // ============================================================
    // 403 ERROR PAGE
    // ============================================================
});
