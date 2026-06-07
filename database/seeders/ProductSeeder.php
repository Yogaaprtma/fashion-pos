<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Suppliers
        $suppliers = [
            ['name' => 'PT Garmen Nusantara', 'contact_person' => 'Bapak Ahmad', 'phone' => '021-5551234', 'email' => 'pt.garmen@example.com'],
            ['name' => 'CV Mode Textile', 'contact_person' => 'Ibu Sari', 'phone' => '021-5555678', 'email' => 'cv.mode@example.com'],
            ['name' => 'UD Berkah Sandang', 'contact_person' => 'Bapak Hendra', 'phone' => '021-5559012', 'email' => 'ud.berkah@example.com'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(['name' => $sup['name']], $sup);
        }

        // Products
        $categories = Category::whereNotNull('parent_id')->get()->keyBy('slug');

        $products = [
            [
                'category_slug' => 'kaos-t-shirt',
                'name' => 'Kaos Polos Premium',
                'sku' => 'KPS-001',
                'brand' => 'BasicWear',
                'buy_price' => 45000,
                'sell_price' => 89000,
                'min_stock' => 10,
                'variants' => [
                    ['size' => 'S', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 25],
                    ['size' => 'M', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 30],
                    ['size' => 'L', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 20],
                    ['size' => 'XL', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 15],
                    ['size' => 'S', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 20],
                    ['size' => 'M', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 25],
                    ['size' => 'L', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 18],
                    ['size' => 'S', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 15],
                    ['size' => 'M', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 20],
                    ['size' => 'L', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 12],
                ],
            ],
            [
                'category_slug' => 'polo-shirt',
                'name' => 'Polo Shirt Pique',
                'sku' => 'PLP-001',
                'brand' => 'PoloCraft',
                'buy_price' => 65000,
                'sell_price' => 125000,
                'min_stock' => 8,
                'variants' => [
                    ['size' => 'S', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 12],
                    ['size' => 'M', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 18],
                    ['size' => 'L', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 14],
                    ['size' => 'XL', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 10],
                    ['size' => 'M', 'color' => 'Biru Muda', 'color_hex' => '#93c5fd', 'stock_qty' => 12],
                    ['size' => 'L', 'color' => 'Biru Muda', 'color_hex' => '#93c5fd', 'stock_qty' => 8],
                ],
            ],
            [
                'category_slug' => 'kemeja',
                'name' => 'Kemeja Formal Slim Fit',
                'sku' => 'KFS-001',
                'brand' => 'FormalEdge',
                'buy_price' => 90000,
                'sell_price' => 175000,
                'min_stock' => 5,
                'variants' => [
                    ['size' => 'S', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 8],
                    ['size' => 'M', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 12],
                    ['size' => 'L', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 10],
                    ['size' => 'XL', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 6],
                    ['size' => 'M', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 8],
                    ['size' => 'L', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 6],
                    ['size' => 'M', 'color' => 'Biru', 'color_hex' => '#3b82f6', 'stock_qty' => 10],
                    ['size' => 'L', 'color' => 'Biru', 'color_hex' => '#3b82f6', 'stock_qty' => 8],
                ],
            ],
            [
                'category_slug' => 'celana-jeans',
                'name' => 'Celana Jeans Slim Fit',
                'sku' => 'CJS-001',
                'brand' => 'DenimCo',
                'buy_price' => 130000,
                'sell_price' => 250000,
                'min_stock' => 5,
                'variants' => [
                    ['size' => '28', 'color' => 'Biru Tua', 'color_hex' => '#1e3a5f', 'stock_qty' => 8],
                    ['size' => '29', 'color' => 'Biru Tua', 'color_hex' => '#1e3a5f', 'stock_qty' => 10],
                    ['size' => '30', 'color' => 'Biru Tua', 'color_hex' => '#1e3a5f', 'stock_qty' => 12],
                    ['size' => '31', 'color' => 'Biru Tua', 'color_hex' => '#1e3a5f', 'stock_qty' => 10],
                    ['size' => '32', 'color' => 'Biru Tua', 'color_hex' => '#1e3a5f', 'stock_qty' => 8],
                    ['size' => '30', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 10],
                    ['size' => '31', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 8],
                    ['size' => '32', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 6],
                    ['size' => '33', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 4],
                ],
            ],
            [
                'category_slug' => 'jaket',
                'name' => 'Jaket Hoodie Fleece',
                'sku' => 'JHF-001',
                'brand' => 'WarmStyle',
                'buy_price' => 180000,
                'sell_price' => 350000,
                'min_stock' => 5,
                'variants' => [
                    ['size' => 'S', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 6],
                    ['size' => 'M', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 10],
                    ['size' => 'L', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 8],
                    ['size' => 'XL', 'color' => 'Abu-abu', 'color_hex' => '#9ca3af', 'stock_qty' => 4],
                    ['size' => 'M', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 8],
                    ['size' => 'L', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 6],
                    ['size' => 'XL', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 3],
                ],
            ],
            [
                'category_slug' => 'celana-chino',
                'name' => 'Celana Chino Slim',
                'sku' => 'CCS-001',
                'brand' => 'UrbanWear',
                'buy_price' => 85000,
                'sell_price' => 165000,
                'min_stock' => 5,
                'variants' => [
                    ['size' => '28', 'color' => 'Khaki', 'color_hex' => '#c4a882', 'stock_qty' => 10],
                    ['size' => '29', 'color' => 'Khaki', 'color_hex' => '#c4a882', 'stock_qty' => 12],
                    ['size' => '30', 'color' => 'Khaki', 'color_hex' => '#c4a882', 'stock_qty' => 14],
                    ['size' => '31', 'color' => 'Khaki', 'color_hex' => '#c4a882', 'stock_qty' => 10],
                    ['size' => '29', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 8],
                    ['size' => '30', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 10],
                    ['size' => '31', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 6],
                ],
            ],
            [
                'category_slug' => 'sweater-hoodie',
                'name' => 'Sweater Crewneck Polos',
                'sku' => 'SCP-001',
                'brand' => 'CozyWear',
                'buy_price' => 95000,
                'sell_price' => 189000,
                'min_stock' => 5,
                'variants' => [
                    ['size' => 'S', 'color' => 'Cream', 'color_hex' => '#f5f0e8', 'stock_qty' => 8],
                    ['size' => 'M', 'color' => 'Cream', 'color_hex' => '#f5f0e8', 'stock_qty' => 12],
                    ['size' => 'L', 'color' => 'Cream', 'color_hex' => '#f5f0e8', 'stock_qty' => 10],
                    ['size' => 'M', 'color' => 'Sage Green', 'color_hex' => '#86a789', 'stock_qty' => 8],
                    ['size' => 'L', 'color' => 'Sage Green', 'color_hex' => '#86a789', 'stock_qty' => 6],
                ],
            ],
            [
                'category_slug' => 'topi',
                'name' => 'Topi Baseball Cap',
                'sku' => 'TBC-001',
                'brand' => 'CapStyle',
                'buy_price' => 25000,
                'sell_price' => 55000,
                'min_stock' => 10,
                'variants' => [
                    ['size' => 'OneSize', 'color' => 'Hitam', 'color_hex' => '#000000', 'stock_qty' => 25],
                    ['size' => 'OneSize', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'stock_qty' => 20],
                    ['size' => 'OneSize', 'color' => 'Navy', 'color_hex' => '#1e3a5f', 'stock_qty' => 15],
                    ['size' => 'OneSize', 'color' => 'Merah', 'color_hex' => '#ef4444', 'stock_qty' => 12],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);

            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);

            $category = $categories->get($categorySlug);
            if (!$category) continue;

            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                array_merge($productData, ['category_id' => $category->id, 'is_active' => true])
            );

            foreach ($variants as $variantData) {
                $skuVariant = $product->sku . '-' . $variantData['size'] . '-' . strtoupper(substr($variantData['color'], 0, 3));
                ProductVariant::updateOrCreate(
                    ['sku_variant' => $skuVariant],
                    array_merge($variantData, [
                        'product_id' => $product->id,
                        'sku_variant' => $skuVariant,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
