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

        $this->stockService->adjustStock(
            $request->product_variant_id,
            $request->new_quantity,
            $request->notes
        );

        return back()->with('success', 'Stok berhasil diperbarui!');
    }

    public function restockAssistant()
    {
        $startDate = now()->subDays(30);

        $salesData = \App\Models\TransactionItem::select('product_variant_id', \DB::raw('SUM(quantity) as sold_qty'))
            ->whereHas('transaction', fn($q) => $q->where('status', 'completed')->where('created_at', '>=', $startDate))
            ->groupBy('product_variant_id')
            ->pluck('sold_qty', 'product_variant_id');

        $variants = ProductVariant::with(['product'])->where('is_active', true)->get();

        $stats = ['critical' => 0, 'warning' => 0, 'attention' => 0, 'safe' => 0];
        $recommendations = [];

        foreach ($variants as $variant) {
            $sold30d = (int)($salesData[$variant->id] ?? 0);
            $avgPerDay = $sold30d / 30.0;
            $stock = (int)$variant->stock_qty;

            if ($avgPerDay > 0) {
                $daysRemaining = (int)floor($stock / $avgPerDay);
            } else {
                $daysRemaining = null;
            }

            if ($daysRemaining !== null && $daysRemaining <= 7) {
                $status = 'critical';
                $stats['critical']++;
                $recommendedQty = max(10, (int)ceil($avgPerDay * 30) - $stock);
                $recommendation = "💡 Pesan secepatnya minimal {$recommendedQty} pcs!";
            } elseif ($daysRemaining !== null && $daysRemaining <= 14) {
                $status = 'warning';
                $stats['warning']++;
                $recommendedQty = max(5, (int)ceil($avgPerDay * 30) - $stock);
                $recommendation = "⚠️ Buat Purchase Order (PO) minggu ini (~{$recommendedQty} pcs).";
            } elseif ($daysRemaining !== null && $daysRemaining <= 30) {
                $status = 'attention';
                $stats['attention']++;
                $recommendation = "ℹ️ Pantau stok, cukup untuk ~{$daysRemaining} hari.";
            } else {
                $status = 'safe';
                $stats['safe']++;
                $recommendation = null;
            }

            $recommendations[] = [
                'product_name'   => $variant->product->name ?? 'Produk',
                'variant_label'  => $variant->variant_label,
                'sku'            => $variant->sku,
                'stock'          => $stock,
                'sold_30d'       => $sold30d,
                'avg_per_day'    => $avgPerDay,
                'days_remaining' => $daysRemaining,
                'status'         => $status,
                'recommendation' => $recommendation,
            ];
        }

        usort($recommendations, function ($a, $b) {
            $order = ['critical' => 1, 'warning' => 2, 'attention' => 3, 'safe' => 4];
            return $order[$a['status']] <=> $order[$b['status']];
        });

        return view('inventory.restock-assistant', compact('recommendations', 'stats'));
    }
}
