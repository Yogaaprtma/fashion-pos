<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\ProductVariant;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Abaikan baris jika nama produk kosong
            if (empty($row['nama_produk'])) continue;

            // Cari atau buat kategori
            $category = null;
            if (!empty($row['kategori'])) {
                $category = Category::firstOrCreate(
                    ['name' => trim($row['kategori'])],
                    ['slug' => Str::slug($row['kategori'])]
                );
            }

            // Cari atau buat Produk
            $product = Product::firstOrCreate(
                ['name' => trim($row['nama_produk'])],
                [
                    'category_id' => $category?->id,
                    'slug'        => Str::slug($row['nama_produk']) . '-' . Str::random(5),
                    'sku'         => $row['sku_induk'] ?? strtoupper(Str::random(6)),
                    'brand'       => $row['merk'] ?? null,
                    'description' => $row['deskripsi'] ?? null,
                    'buy_price'   => $row['harga_beli'] ?? 0,
                    'sell_price'  => $row['harga_jual'] ?? 0,
                    'min_stock'   => $row['minimum_stok'] ?? 5,
                    'is_active'   => true,
                ]
            );

            // Buat Varian (Size/Warna)
            $size = trim($row['ukuran'] ?? 'All Size');
            $color = trim($row['warna'] ?? 'Default');
            $skuVariant = trim($row['sku_varian'] ?? '');

            if (empty($skuVariant)) {
                $skuVariant = $product->sku . '-' . strtoupper(substr($size, 0, 2)) . strtoupper(substr($color, 0, 2));
            }

            $variant = ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => $color,
                ],
                [
                    'sku_variant' => $skuVariant,
                    'stock_qty' => $row['stok_awal'] ?? 0,
                ]
            );

            // Jika varian sudah ada dan butuh penambahan stok saat import awal, kita tambahkan (Opsional)
            if (!$variant->wasRecentlyCreated && isset($row['stok_awal']) && $row['stok_awal'] > 0) {
                $variant->increment('stock_qty', $row['stok_awal']);
            }
        }
    }
}
