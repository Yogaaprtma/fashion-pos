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
use App\Http\Controllers\Inventory\PromotionController;
use App\Http\Controllers\POS\QrisController;

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
        Route::get('/cds', [CashierController::class, 'cds'])->name('cds');

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
        Route::get('/transaction/{transaction}/whatsapp', [TransactionController::class, 'whatsapp'])->name('transaction.whatsapp');
        Route::get('/history', [TransactionController::class, 'history'])->name('history');

        // Hold & Recall
        Route::post('/transaction/{transaction}/hold', [TransactionController::class, 'hold'])->name('transaction.hold');
        Route::post('/transaction/{transaction}/recall', [TransactionController::class, 'recall'])->name('transaction.recall');
        Route::get('/held', [TransactionController::class, 'heldList'])->name('held');
        Route::post('/coupon/check', [PromotionController::class, 'checkCoupon'])->name('coupon.check');

        // QRIS Dinamis
        Route::post('/qris/generate', [QrisController::class, 'generate'])->name('qris.generate');
        Route::get('/qris/check/{orderId}', [QrisController::class, 'check'])->name('qris.check');
    });

    // ============================================================
    // INVENTORY ROUTES
    // ============================================================
    Route::prefix('inventory')->name('inventory.')->middleware('permission:inventory.view')->group(function () {
        Route::get('/barcode-generator', [ProductController::class, 'barcodeGenerator'])->name('barcode-generator');
        Route::post('/products/import', [ProductController::class, 'importExcel'])->name('products.import');
        Route::get('/products/download-template', [ProductController::class, 'downloadTemplate'])->name('products.download-template');
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/upload-image', [ProductController::class, 'uploadImage'])->name('products.upload-image');
        Route::delete('/products/{product}/delete-image/{image}', [ProductController::class, 'deleteImage'])->name('products.delete-image');

        // Barcode Labels
        Route::get('/barcode-labels', [\App\Http\Controllers\Inventory\BarcodeLabelController::class, 'index'])->name('barcode-labels.index');
        Route::post('/barcode-labels/print', [\App\Http\Controllers\Inventory\BarcodeLabelController::class, 'print'])->name('barcode-labels.print');

        // Product Bundles / Kitting
        Route::get('/bundles/search', [\App\Http\Controllers\Inventory\ProductBundleController::class, 'searchBundles'])->name('bundles.search');
        Route::resource('bundles', \App\Http\Controllers\Inventory\ProductBundleController::class);

        // Categories
        Route::get('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Inventory\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Inventory\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Stock
        Route::get('/stock', [\App\Http\Controllers\Inventory\StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/movements', [\App\Http\Controllers\Inventory\StockController::class, 'movements'])->name('stock.movements');
        Route::get('/stock/low', [\App\Http\Controllers\Inventory\StockController::class, 'lowStock'])->name('stock.low');
        Route::get('/stock/restock-assistant', [\App\Http\Controllers\Inventory\StockController::class, 'restockAssistant'])->name('stock.restock-assistant');
        Route::post('/stock/adjust', [\App\Http\Controllers\Inventory\StockController::class, 'adjust'])->middleware('permission:inventory.manage')->name('stock.adjust');

        // Stock Opname
        Route::get('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'index'])->name('opname.index');
        Route::post('/opname', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'store'])->middleware('permission:inventory.opname')->name('opname.store');
        Route::get('/opname/{opname}', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'show'])->name('opname.show');
        Route::post('/opname/{opname}/approve', [\App\Http\Controllers\Inventory\StockOpnameController::class, 'approve'])->middleware('permission:inventory.opname')->name('opname.approve');

        // Promotions & Vouchers
        Route::resource('promotions', PromotionController::class);

        // Stock Transfers (Mutasi Stok)
        Route::resource('transfers', \App\Http\Controllers\Inventory\StockTransferController::class);
    });

    // ============================================================
    // PURCHASE ROUTES
    // ============================================================
    Route::prefix('purchase')->name('purchase.')->middleware('permission:purchase.view')->group(function () {
        Route::resource('suppliers', \App\Http\Controllers\Purchase\SupplierController::class);
        Route::resource('orders', \App\Http\Controllers\Purchase\PurchaseOrderController::class);
        Route::post('/orders/{order}/receive', [\App\Http\Controllers\Purchase\PurchaseOrderController::class, 'receive'])->name('orders.receive');

        // Supplier Debts (Hutang Supplier)
        Route::get('/supplier-debts', [\App\Http\Controllers\POS\DebtController::class, 'suppliersIndex'])->name('debts.suppliers.index');
        Route::post('/supplier-debts/{purchaseOrder}/pay', [\App\Http\Controllers\POS\DebtController::class, 'paySupplierDebt'])->name('debts.suppliers.pay');
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
        Route::get('/commission', [ReportController::class, 'commissionIndex'])->name('commission');
        Route::get('/sales/export-pdf', [ReportController::class, 'exportSalesPdf'])->name('sales.export-pdf');
        Route::get('/sales/export-excel', [ReportController::class, 'exportSalesExcel'])->name('sales.export-excel');
        Route::get('/cashier', [ReportController::class, 'cashierReport'])->name('cashier');
        Route::get('/discount', [ReportController::class, 'discountReport'])->name('discount');
        Route::get('/category', [ReportController::class, 'categoryReport'])->name('category');
        Route::get('/payment-method', [ReportController::class, 'paymentMethodReport'])->name('payment-method');
        Route::get('/returns-voids', [ReportController::class, 'returnsVoidsReport'])->name('returns-voids');
        Route::get('/busy-hours', [ReportController::class, 'busyHoursReport'])->name('busy-hours');
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
    // CUSTOMERS (MEMBER) ROUTES
    // ============================================================
    Route::prefix('customers')->name('customers.')->middleware('permission:customer.manage')->group(function () {
        Route::get('/search', [\App\Http\Controllers\CustomerController::class, 'search'])->name('search');

        // Customer Debts (Piutang / Kasbon) - must be before resource to avoid {customer} wildcard match
        Route::get('/debts', [\App\Http\Controllers\POS\DebtController::class, 'customersIndex'])->name('debts.customers.index');
        Route::post('/debts/{transaction}/pay', [\App\Http\Controllers\POS\DebtController::class, 'payCustomerDebt'])->name('debts.customers.pay');

        Route::resource('/', \App\Http\Controllers\CustomerController::class)->parameters(['' => 'customer']);
    });

    // ============================================================
    // LOYALTY POINTS ROUTES
    // ============================================================
    Route::prefix('loyalty')->name('loyalty.')->middleware('permission:customer.manage')->group(function () {
        Route::get('/', [\App\Http\Controllers\LoyaltyController::class, 'index'])->name('index');
        Route::get('/{customer}', [\App\Http\Controllers\LoyaltyController::class, 'show'])->name('show');
        Route::post('/{customer}/adjust', [\App\Http\Controllers\LoyaltyController::class, 'adjust'])->name('adjust');
    });

    // ============================================================
    // USER MANAGEMENT (Admin only)
    // ============================================================
    Route::resource('users', UserController::class)->except(['show'])->middleware('permission:user.manage');

    // ============================================================
    // EXPENSE ROUTES (Biaya & Pengeluaran)
    // ============================================================
    Route::resource('expenses', \App\Http\Controllers\Finance\ExpenseController::class)->middleware('permission:report.financial');

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
        
        // Branches
        Route::resource('branches', \App\Http\Controllers\Setting\BranchController::class)->except(['show']);

        // E-Commerce Integrations
        Route::get('/integrations', [\App\Http\Controllers\Setting\IntegrationController::class, 'index'])->name('integrations.index');
        Route::post('/integrations', [\App\Http\Controllers\Setting\IntegrationController::class, 'store'])->name('integrations.store');
        Route::patch('/integrations/{integration}/toggle', [\App\Http\Controllers\Setting\IntegrationController::class, 'toggle'])->name('integrations.toggle');
        Route::delete('/integrations/{integration}', [\App\Http\Controllers\Setting\IntegrationController::class, 'destroy'])->name('integrations.destroy');
    });

    // ============================================================
    // EXTERNAL API WEBHOOK SYNC
    // ============================================================
    Route::get('/api/v1/external/stock-sync', [\App\Http\Controllers\Api\ExternalSyncController::class, 'getStock']);
    Route::post('/api/v1/external/orders', [\App\Http\Controllers\Api\ExternalSyncController::class, 'handleOnlineOrder']);

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
