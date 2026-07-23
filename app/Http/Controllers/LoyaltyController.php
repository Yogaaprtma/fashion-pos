<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPointHistory;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    // Halaman utama: daftar pelanggan beserta poin
    public function index(Request $request)
    {
        $query = Customer::where('is_member', true)
            ->withCount(['transactions as total_transactions' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['transactions as total_spent_sum' => fn($q) => $q->where('status', 'completed')], 'grand_total');

        if ($search = $request->search) {
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%"));
        }

        if ($tier = $request->tier) {
            match ($tier) {
                'gold'   => $query->where('points', '>=', 5000),
                'silver' => $query->whereBetween('points', [1000, 4999]),
                'bronze' => $query->where('points', '<', 1000),
                default  => null,
            };
        }

        $customers = $query->orderByDesc('points')->paginate(20)->withQueryString();

        $stats = [
            'total_members'   => Customer::where('is_member', true)->count(),
            'total_gold'      => Customer::where('is_member', true)->where('points', '>=', 5000)->count(),
            'total_silver'    => Customer::where('is_member', true)->whereBetween('points', [1000, 4999])->count(),
            'total_bronze'    => Customer::where('is_member', true)->where('points', '<', 1000)->count(),
            'total_points'    => Customer::sum('points'),
            'redeemed_today'  => CustomerPointHistory::whereDate('created_at', today())
                ->where('type', 'redeem')->sum('amount'),
        ];

        return view('loyalty.index', compact('customers', 'stats'));
    }

    // Detail riwayat poin satu pelanggan
    public function show(Customer $customer)
    {
        $histories = $customer->pointHistories()
            ->with('transaction')
            ->latest()
            ->paginate(20);

        return view('loyalty.show', compact('customer', 'histories'));
    }

    // Penyesuaian poin manual oleh admin
    public function adjust(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount'      => 'required|integer|not_in:0',
            'description' => 'required|string|max:255',
        ]);

        $type = $validated['amount'] > 0 ? 'earn' : 'redeem';

        CustomerPointHistory::create([
            'customer_id' => $customer->id,
            'amount'      => $validated['amount'],
            'type'        => $type,
            'description' => $validated['description'],
        ]);

        $customer->increment('points', $validated['amount']);

        // Ensure points don't go negative
        if ($customer->fresh()->points < 0) {
            $customer->update(['points' => 0]);
        }

        return back()->with('success', 'Poin pelanggan berhasil disesuaikan.');
    }

    // Halaman Low Stock Alert
    public function lowStockAlert()
    {
        $lowStockProducts = \App\Models\Product::with(['variants' => fn($q) => $q->orderBy('stock')])
            ->get()
            ->filter(fn($p) => $p->isLowStock())
            ->sortBy(fn($p) => $p->variants->min('stock'))
            ->values();

        $outOfStockCount    = \App\Models\ProductVariant::where('stock', 0)->count();
        $criticalCount      = \App\Models\ProductVariant::where('stock', '>', 0)->where('stock', '<=', 3)->count();
        $lowCount           = \App\Models\ProductVariant::where('stock', '>', 3)->where('stock', '<=', 10)->count();

        return view('inventory.low-stock', compact('lowStockProducts', 'outOfStockCount', 'criticalCount', 'lowCount'));
    }
}
