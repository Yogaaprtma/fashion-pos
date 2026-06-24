<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\CashierSession;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\StoreSetting;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index()
    {
        // Check if user has active session
        $session = auth()->user()->activeSession();

        if (!$session) {
            return redirect()->route('pos.session.open');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $categories = \App\Models\Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $storeName = StoreSetting::get('store_name', 'FashionPOS');

        return view('pos.cashier', compact('session', 'paymentMethods', 'categories', 'storeName'));
    }

    public function openSession()
    {
        // Check if already has open session
        $existing = auth()->user()->activeSession();
        if ($existing) {
            return redirect()->route('pos.index');
        }

        return view('pos.session-open');
    }

    public function startSession(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        // Close any existing open sessions for this user (safety)
        CashierSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->update(['status' => 'closed', 'closed_at' => now()]);

        $session = CashierSession::create([
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_balance' => $request->opening_balance,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Sesi kasir dibuka. Selamat bekerja!');
    }

    public function closeSession(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = auth()->user()->activeSession();
        if (!$session) {
            return back()->withErrors(['error' => 'Tidak ada sesi aktif.']);
        }

        $cashPayments = $session->transactions()
            ->where('status', 'completed')
            ->with('payments.paymentMethod')
            ->get()
            ->flatMap->payments
            ->filter(fn($p) => $p->paymentMethod->type === 'cash')
            ->sum('amount');

        $expectedBalance = $session->opening_balance + $cashPayments;
        $difference = $request->closing_balance - $expectedBalance;

        $session->update([
            'closed_at' => now(),
            'closing_balance' => $request->closing_balance,
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        return redirect()->route('pos.session.report', $session->id)
            ->with('success', 'Sesi kasir ditutup berhasil.');
    }

    public function sessionReport(CashierSession $session)
    {
        $session->load(['user', 'transactions.items', 'transactions.payments.paymentMethod']);
        $storeName = StoreSetting::get('store_name', 'FashionPOS');
        return view('pos.session-report', compact('session', 'storeName'));
    }

    // API: Search products for POS
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::with(['variants' => fn($q) => $q->where('is_active', true)->where('stock_qty', '>', 0), 'category', 'images'])
            ->where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('name', 'like', "%{$query}%")
                       ->orWhere('sku', 'like', "%{$query}%")
                       ->orWhere('barcode', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->take(30)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'brand' => $product->brand,
                    'image_url' => $product->image_url,
                    'variants' => $product->variants->map(fn($v) => [
                        'id' => $v->id,
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'size' => $v->size,
                        'color' => $v->color,
                        'color_hex' => $v->color_hex,
                        'label' => $v->variant_label,
                        'sku_variant' => $v->sku_variant,
                        'sell_price' => $v->effective_sell_price,
                        'stock_qty' => $v->stock_qty,
                    ]),
                ];
            });

        return response()->json($products);
    }

    // API: Get product by barcode
    public function getByBarcode(Request $request)
    {
        $barcode = $request->get('barcode');

        $variant = ProductVariant::with(['product.category', 'product.images'])
            ->where('barcode_variant', $barcode)
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            $product = Product::with(['variants' => fn($q) => $q->where('is_active', true), 'images'])
                ->where('barcode', $barcode)
                ->first();

            if ($product && $product->variants->count() === 1) {
                $variant = $product->variants->first();
            }
        }

        if (!$variant) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $variant->id,
            'product_id' => $variant->product_id,
            'category_id' => $variant->product->category_id,
            'product_name' => $variant->product->name,
            'variant_label' => $variant->variant_label,
            'sell_price' => $variant->effective_sell_price,
            'stock_qty' => $variant->stock_qty,
        ]);
    }
}
