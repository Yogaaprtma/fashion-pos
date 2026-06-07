<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreSetting;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'store_name', 'value' => 'FashionPOS Swalayan', 'group' => 'general', 'type' => 'string'],
            ['key' => 'store_tagline', 'value' => 'Toko Pakaian Terlengkap', 'group' => 'general', 'type' => 'string'],
            ['key' => 'store_address', 'value' => 'Jl. Fashion No. 1, Jakarta', 'group' => 'general', 'type' => 'string'],
            ['key' => 'store_phone', 'value' => '021-12345678', 'group' => 'general', 'type' => 'string'],
            ['key' => 'store_email', 'value' => 'info@fashionpos.com', 'group' => 'general', 'type' => 'string'],
            ['key' => 'store_logo', 'value' => null, 'group' => 'general', 'type' => 'string'],
            // Tax
            ['key' => 'tax_enabled', 'value' => '0', 'group' => 'tax', 'type' => 'boolean'],
            ['key' => 'tax_percent', 'value' => '11', 'group' => 'tax', 'type' => 'number'],
            ['key' => 'tax_name', 'value' => 'PPN', 'group' => 'tax', 'type' => 'string'],
            // Receipt
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja!', 'group' => 'receipt', 'type' => 'string'],
            ['key' => 'receipt_show_logo', 'value' => '1', 'group' => 'receipt', 'type' => 'boolean'],
            ['key' => 'receipt_print_copies', 'value' => '1', 'group' => 'receipt', 'type' => 'number'],
            // System
            ['key' => 'low_stock_threshold', 'value' => '5', 'group' => 'system', 'type' => 'number'],
            ['key' => 'currency', 'value' => 'IDR', 'group' => 'system', 'type' => 'string'],
            ['key' => 'currency_symbol', 'value' => 'Rp', 'group' => 'system', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            StoreSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
