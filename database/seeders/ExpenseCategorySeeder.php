<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Listrik, Air & Internet', 'description' => 'Biaya bulanan utilitas toko'],
            ['name' => 'Gaji & Bonus Karyawan', 'description' => 'Pembayaran honor staf kasir dan penjaga toko'],
            ['name' => 'Sewa Tempat / Ruko', 'description' => 'Biaya kontrak sewa bangunan outlet'],
            ['name' => 'Operasional Harian', 'description' => 'Biaya harian seperti bensin, kebersihan, parkir, dll'],
            ['name' => 'Pemasaran & Iklan', 'description' => 'Biaya promosi media sosial, brosur, banner'],
            ['name' => 'Perlengkapan & Packing', 'description' => 'Pembelian paper bag, kantong plastik, kertas struk thermal, dll'],
            ['name' => 'Lain-lain', 'description' => 'Pengeluaran tidak terduga lainnya'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
