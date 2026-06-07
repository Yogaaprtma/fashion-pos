<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Category;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'images'])
            ->withTrashed($request->has('show_deleted'));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%')
                    ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->stock_status === 'low') {
            $query->whereHas('variants', fn($q) => $q->where('stock_qty', '<=', 5));
        } elseif ($request->stock_status === 'out') {
            $query->whereHas('variants', fn($q) => $q->where('stock_qty', '<=', 0));
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $lowStockCount = $this->stockService->getLowStockProducts()->count();

        return view('inventory.products.index', compact('products', 'categories', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->with('parent')->get();
        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'min_stock' => 'required|integer|min:0',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'nullable|string',
            'variants.*.color' => 'nullable|string',
            'variants.*.stock_qty' => 'required|integer|min:0',
            'variants.*.sell_price' => 'nullable|numeric',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'sku' => strtoupper(Str::random(3)) . '-' . date('ymd') . '-' . rand(100, 999),
            'barcode' => $request->barcode,
            'description' => $request->description,
            'brand' => $request->brand,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'min_stock' => $request->min_stock,
            'is_active' => true,
        ]);

        // Create variants
        foreach ($request->variants as $index => $variantData) {
            $size = $variantData['size'] ?? 'OneSize';
            $color = $variantData['color'] ?? 'Default';
            $skuVariant = $product->sku . '-' . $size . '-' . strtoupper(substr($color, 0, 3));

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'color_hex' => $variantData['color_hex'] ?? null,
                'sku_variant' => $skuVariant . '-' . rand(10, 99),
                'sell_price' => $variantData['sell_price'] ?? null,
                'buy_price' => $variantData['buy_price'] ?? null,
                'stock_qty' => $variantData['stock_qty'],
                'is_active' => true,
            ]);

            // Log initial stock
            if ($variantData['stock_qty'] > 0) {
                \App\Models\StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'in',
                    'quantity' => $variantData['stock_qty'],
                    'stock_before' => 0,
                    'stock_after' => $variantData['stock_qty'],
                    'reference_type' => 'initial_stock',
                    'notes' => 'Stok awal produk baru',
                    'user_id' => auth()->id(),
                ]);
            }
        }

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product-images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('inventory.products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants.stockMovements' => fn($q) => $q->latest()->take(5), 'images']);
        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'images']);
        $categories = Category::where('is_active', true)->with('parent')->get();
        return view('inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $product->update($request->only([
            'name',
            'category_id',
            'buy_price',
            'sell_price',
            'brand',
            'description',
            'min_stock',
            'barcode',
            'is_active',
        ]));

        return redirect()->route('inventory.products.show', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function barcodeGenerator(Request $request)
    {
        $products = Product::with(['variants' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        return view('inventory.products.barcode', compact('products'));
    }
}
