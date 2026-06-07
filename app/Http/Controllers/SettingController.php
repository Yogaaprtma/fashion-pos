<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Models\PaymentMethod;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::all()->keyBy('key');
        $paymentMethods = PaymentMethod::all();
        return view('settings.index', compact('settings', 'paymentMethods'));
    }

    public function updateStore(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:100',
            'store_tagline' => 'nullable|string|max:200',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:150',
            'logo' => 'nullable|image|max:2048',
        ]);

        $keys = ['store_name', 'store_tagline', 'store_address', 'store_phone', 'store_email'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                StoreSetting::set($key, $request->$key);
            }
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('store', 'public');
            StoreSetting::set('store_logo', $path);
        }

        AuditLog::record('update_store_settings');
        return back()->with('success', 'Informasi toko berhasil diperbarui.');
    }

    public function updateTax(Request $request)
    {
        $request->validate([
            'tax_enabled' => 'boolean',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:20',
        ]);

        StoreSetting::set('tax_enabled', $request->boolean('tax_enabled') ? '1' : '0');
        StoreSetting::set('tax_percent', $request->tax_percent);
        StoreSetting::set('tax_name', $request->tax_name);

        return back()->with('success', 'Pengaturan pajak berhasil diperbarui.');
    }

    public function updateReceipt(Request $request)
    {
        $request->validate([
            'receipt_footer' => 'nullable|string|max:500',
            'receipt_show_logo' => 'boolean',
            'receipt_print_copies' => 'required|integer|min:1|max:3',
        ]);

        StoreSetting::set('receipt_footer', $request->receipt_footer);
        StoreSetting::set('receipt_show_logo', $request->boolean('receipt_show_logo') ? '1' : '0');
        StoreSetting::set('receipt_print_copies', $request->receipt_print_copies);

        return back()->with('success', 'Pengaturan struk berhasil diperbarui.');
    }

    public function updatePaymentMethods(Request $request)
    {
        $activeIds = $request->active_payment_methods ?? [];

        PaymentMethod::query()->update(['is_active' => false]);
        if (!empty($activeIds)) {
            PaymentMethod::whereIn('id', $activeIds)->update(['is_active' => true]);
        }

        return back()->with('success', 'Metode pembayaran berhasil diperbarui.');
    }
}
