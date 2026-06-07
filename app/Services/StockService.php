<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Product;

class StockService
{
    public function addStock(ProductVariant $variant, int $qty, string $referenceType, int $referenceId, string $notes = ''): StockMovement
    {
        $stockBefore = $variant->stock_qty;
        $variant->increment('stock_qty', $qty);
        $variant->refresh();

        return StockMovement::create([
            'product_variant_id' => $variant->id,
            'type' => 'in',
            'quantity' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $variant->stock_qty,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }

    public function adjustStock(ProductVariant $variant, int $newQty, string $notes = ''): StockMovement
    {
        $stockBefore = $variant->stock_qty;
        $difference = $newQty - $stockBefore;

        $variant->update(['stock_qty' => $newQty]);

        return StockMovement::create([
            'product_variant_id' => $variant->id,
            'type' => 'adjustment',
            'quantity' => $difference,
            'stock_before' => $stockBefore,
            'stock_after' => $newQty,
            'reference_type' => 'manual_adjustment',
            'reference_id' => null,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }

    public function getLowStockProducts(int $threshold = null)
    {
        return Product::with(['variants', 'category'])
            ->whereHas('variants', function ($q) use ($threshold) {
                $q->where('is_active', true)
                  ->when($threshold, fn($q2) => $q2->where('stock_qty', '<=', $threshold));
            })
            ->get()
            ->filter(fn($product) => $product->isLowStock());
    }

    public function getStockValue()
    {
        return ProductVariant::with('product')
            ->where('is_active', true)
            ->get()
            ->sum(fn($v) => $v->effective_buy_price * $v->stock_qty);
    }

    public function getTopSellingProducts(string $period = 'month', int $limit = 10)
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        return \App\Models\TransactionItem::with(['productVariant.product'])
            ->whereHas('transaction', fn($q) => $q->where('status', 'completed')->where('created_at', '>=', $startDate))
            ->selectRaw('product_variant_id, product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_variant_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function getSalesChart(string $period = 'week'): array
    {
        $labels = [];
        $values = [];

        if ($period === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('D, d M');
                $values[] = \App\Models\Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('grand_total');
            }
        } elseif ($period === 'month') {
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('d M');
                $values[] = \App\Models\Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('grand_total');
            }
        } elseif ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');
                $values[] = \App\Models\Transaction::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->sum('grand_total');
            }
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
