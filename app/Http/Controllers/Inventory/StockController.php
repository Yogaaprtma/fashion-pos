<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = ProductVariant::with(['product.category'])
            ->where('is_active', true)
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('product', fn($p) => $p->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhere('sku_variant', 'like', '%' . $request->search . '%');
            })
            ->when($request->category_id, fn($q) => $q->whereHas('product', fn($p) => $p->where('category_id', $request->category_id)));

        $variants = $query->orderBy('stock_qty', 'asc')->paginate(15)->withQueryString();
        $stockValue = $this->stockService->getStockValue();

        return view('inventory.stock.index', compact('variants', 'stockValue'));
    }

    public function movements(Request $request)
    {
        $movements = StockMovement::with(['productVariant.product', 'user'])
            ->latest()
            ->paginate(30)->withQueryString();
        return view('inventory.stock.movements', compact('movements'));
    }

    public function lowStock()
    {
        $lowStockProducts = \App\Models\Product::with(['variants' => fn($q) => $q->orderBy('stock_qty')])
            ->get()
            ->filter(fn($p) => $p->isLowStock())
            ->sortBy(fn($p) => $p->variants->min('stock_qty'))
            ->values();

        $outOfStockCount    = \App\Models\ProductVariant::where('stock_qty', 0)->count();
        $criticalCount      = \App\Models\ProductVariant::where('stock_qty', '>', 0)->where('stock_qty', '<=', 3)->count();
        $lowCount           = \App\Models\ProductVariant::where('stock_qty', '>', 3)->where('stock_qty', '<=', 10)->count();

        return view('inventory.stock.low', compact('lowStockProducts', 'outOfStockCount', 'criticalCount', 'lowCount'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'required|string|min:5',
        ]);

        $variant = ProductVariant::find($request->product_variant_id);
        $this->stockService->adjustStock($variant, $request->new_quantity, $request->notes);

        return back()->with('success', 'Stok berhasil disesuaikan.');
    }
}
