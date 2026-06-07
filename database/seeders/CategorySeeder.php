<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            ['name' => 'Atasan', 'slug' => 'atasan', 'icon' => '👕'],
            ['name' => 'Bawahan', 'slug' => 'bawahan', 'icon' => '👖'],
            ['name' => 'Pakaian Luar', 'slug' => 'pakaian-luar', 'icon' => '🧥'],
            ['name' => 'Aksesoris', 'slug' => 'aksesoris', 'icon' => '👜'],
            ['name' => 'Pakaian Dalam', 'slug' => 'pakaian-dalam', 'icon' => '🩲'],
            ['name' => 'Perlengkapan Tidur', 'slug' => 'perlengkapan-tidur', 'icon' => '🛏️'],
        ];

        foreach ($parents as $parent) {
            Category::updateOrCreate(['slug' => $parent['slug']], $parent);
        }

        $atasan = Category::where('slug', 'atasan')->first();
        $bawahan = Category::where('slug', 'bawahan')->first();
        $pakaianLuar = Category::where('slug', 'pakaian-luar')->first();
        $aksesoris = Category::where('slug', 'aksesoris')->first();

        $children = [
            // Atasan
            ['parent_id' => $atasan->id, 'name' => 'Kaos / T-Shirt', 'slug' => 'kaos-t-shirt', 'icon' => '👕'],
            ['parent_id' => $atasan->id, 'name' => 'Kemeja', 'slug' => 'kemeja', 'icon' => '👔'],
            ['parent_id' => $atasan->id, 'name' => 'Polo Shirt', 'slug' => 'polo-shirt', 'icon' => '👕'],
            ['parent_id' => $atasan->id, 'name' => 'Blouse / Atasan Wanita', 'slug' => 'blouse', 'icon' => '👚'],
            ['parent_id' => $atasan->id, 'name' => 'Sweater / Hoodie', 'slug' => 'sweater-hoodie', 'icon' => '🧥'],
            // Bawahan
            ['parent_id' => $bawahan->id, 'name' => 'Celana Jeans', 'slug' => 'celana-jeans', 'icon' => '👖'],
            ['parent_id' => $bawahan->id, 'name' => 'Celana Chino', 'slug' => 'celana-chino', 'icon' => '👖'],
            ['parent_id' => $bawahan->id, 'name' => 'Celana Pendek', 'slug' => 'celana-pendek', 'icon' => '🩳'],
            ['parent_id' => $bawahan->id, 'name' => 'Rok', 'slug' => 'rok', 'icon' => '👗'],
            ['parent_id' => $bawahan->id, 'name' => 'Celana Olahraga', 'slug' => 'celana-olahraga', 'icon' => '🩳'],
            // Pakaian Luar
            ['parent_id' => $pakaianLuar->id, 'name' => 'Jaket', 'slug' => 'jaket', 'icon' => '🧥'],
            ['parent_id' => $pakaianLuar->id, 'name' => 'Cardigan', 'slug' => 'cardigan', 'icon' => '🧥'],
            ['parent_id' => $pakaianLuar->id, 'name' => 'Jas / Blazer', 'slug' => 'jas-blazer', 'icon' => '🥼'],
            // Aksesoris
            ['parent_id' => $aksesoris->id, 'name' => 'Topi', 'slug' => 'topi', 'icon' => '🧢'],
            ['parent_id' => $aksesoris->id, 'name' => 'Ikat Pinggang', 'slug' => 'ikat-pinggang', 'icon' => '📿'],
            ['parent_id' => $aksesoris->id, 'name' => 'Kaos Kaki', 'slug' => 'kaos-kaki', 'icon' => '🧦'],
            ['parent_id' => $aksesoris->id, 'name' => 'Dasi', 'slug' => 'dasi', 'icon' => '👔'],
        ];

        foreach ($children as $child) {
            Category::updateOrCreate(['slug' => $child['slug']], $child);
        }
    }
}
