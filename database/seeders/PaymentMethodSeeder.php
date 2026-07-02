<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Tunai / Cash', 'type' => 'cash', 'icon' => 'cash', 'is_active' => true],
            ['name' => 'Debit BCA', 'type' => 'debit', 'icon' => 'credit-card', 'is_active' => true],
            ['name' => 'Debit Mandiri', 'type' => 'debit', 'icon' => 'credit-card', 'is_active' => true],
            ['name' => 'Debit BRI', 'type' => 'debit', 'icon' => 'credit-card', 'is_active' => true],
            ['name' => 'QRIS', 'type' => 'qris', 'icon' => 'qr-code', 'is_active' => true],
            ['name' => 'GoPay', 'type' => 'ewallet', 'icon' => 'device-mobile', 'is_active' => true],
            ['name' => 'OVO', 'type' => 'ewallet', 'icon' => 'device-mobile', 'is_active' => true],
            ['name' => 'Dana', 'type' => 'ewallet', 'icon' => 'device-mobile', 'is_active' => true],
            ['name' => 'Transfer Bank', 'type' => 'transfer', 'icon' => 'bank', 'is_active' => true],
            ['name' => 'Kartu Kredit', 'type' => 'credit', 'icon' => 'credit-card', 'is_active' => true],
            ['name' => 'Tempo / Kasbon', 'type' => 'tempo', 'icon' => 'calendar', 'is_active' => true],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(['name' => $method['name']], $method);
        }
    }
}
