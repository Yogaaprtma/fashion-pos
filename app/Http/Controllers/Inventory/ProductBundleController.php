<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductBundleController extends Controller
{
    public function index()
    {
        $bundles = ProductBundle::with(['items.productVariant.product', 'category'])
            ->orderBy('name')
            ->paginate(20);

        return view('inventory.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->orderBy('product_id')
            ->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('inventory.bundles.create', compact('variants', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'bundle_price' => 'required|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $bundle = ProductBundle::create([
                'name'         => $request->name,
                'sku'          => 'BDL-' . strtoupper(Str::random(6)),
                'description'  => $request->description,
                'bundle_price' => $request->bundle_price,
                'is_active'    => $request->boolean('is_active', true),
                'category_id'  => $request->category_id ?: null,
            ]);

            foreach ($request->items as $item) {
                ProductBundleItem::create([
                    'bundle_id'          => $bundle->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity'           => $item['quantity'],
                ]);
            }

            $bundle->load('items.productVariant');
            $bundle->recalculateNormalTotal();
        });

        return redirect()->route('inventory.bundles.index')
            ->with('success', 'Paket produk berhasil dibuat!');
    }

    public function edit(ProductBundle $bundle)
    {
        $bundle->load(['items.productVariant.product']);
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->orderBy('product_id')
            ->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('inventory.bundles.edit', compact('bundle', 'variants', 'categories'));
    }

    public function update(Request $request, ProductBundle $bundle)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'bundle_price' => 'required|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $bundle) {
            $bundle->update([
                'name'         => $request->name,
                'description'  => $request->description,
                'bundle_price' => $request->bundle_price,
                'is_active'    => $request->boolean('is_active', true),
                'category_id'  => $request->category_id ?: null,
            ]);

            $bundle->items()->delete();

            foreach ($request->items as $item) {
                ProductBundleItem::create([
                    'bundle_id'          => $bundle->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity'           => $item['quantity'],
                ]);
            }

            $bundle->load('items.productVariant');
            $bundle->recalculateNormalTotal();
        });

        return redirect()->route('inventory.bundles.index')
            ->with('success', 'Paket produk berhasil diperbarui!');
    }

    public function destroy(ProductBundle $bundle)
    {
        $bundle->delete();
        return redirect()->route('inventory.bundles.index')
            ->with('success', 'Paket produk dihapus.');
    }

    /**
     * API: Search bundles for cashier POS
     */
    public function searchBundles(Request $request)
    {
        $q = $request->q;
        $bundles = ProductBundle::with(['items.productVariant.product'])
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get();

        return response()->json($bundles->map(function ($b) {
            return [
                'id'            => $b->id,
                'name'          => $b->name,
                'sku'           => $b->sku,
                'bundle_price'  => $b->bundle_price,
                'normal_total'  => $b->normal_total,
                'discount_pct'  => $b->discount_percent,
                'items'         => $b->items->map(fn($i) => [
                    'variant_id'   => $i->product_variant_id,
                    'product_name' => $i->productVariant->product->name,
                    'variant_label'=> $i->productVariant->variant_label,
                    'quantity'     => $i->quantity,
                ]),
            ];
        }));
    }
}
