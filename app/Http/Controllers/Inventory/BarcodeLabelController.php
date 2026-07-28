<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class BarcodeLabelController extends Controller
{
    /**
     * Display the barcode label printing page.
     */
    public function index(Request $request)
    {
        $search = $request->q;

        $products = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate(20);

        $storeName = StoreSetting::get('store_name', 'FashionPOS');

        return view('inventory.barcode-labels.index', compact('products', 'search', 'storeName'));
    }

    /**
     * Render the printable label page for selected variants.
     */
    public function print(Request $request)
    {
        $request->validate([
            'variants' => 'required|array|min:1',
            'variants.*.variant_id' => 'required|exists:product_variants,id',
            'variants.*.copies' => 'nullable|integer|min:1|max:100',
        ]);

        $storeName = StoreSetting::get('store_name', 'FashionPOS');
        $labelSize  = $request->label_size ?? '50x30';  // e.g. 50x30, 40x20, 30x20 (mm)
        $showBarcode = $request->boolean('show_barcode', true);
        $showPrice   = $request->boolean('show_price', true);
        $showVariant = $request->boolean('show_variant', true);

        $labels = [];
        foreach ($request->variants as $row) {
            $variant = ProductVariant::with('product')->find($row['variant_id']);
            if (!$variant) continue;

            $copies = max(1, (int)($row['copies'] ?? 1));
            for ($i = 0; $i < $copies; $i++) {
                $labels[] = $variant;
            }
        }

        return view('inventory.barcode-labels.print', compact(
            'labels', 'storeName', 'labelSize', 'showBarcode', 'showPrice', 'showVariant'
        ));
    }
}
