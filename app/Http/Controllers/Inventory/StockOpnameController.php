<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with(['initiatedBy', 'category'])->latest()->paginate(15);
        return view('inventory.opname.index', compact('opnames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $opname = StockOpname::create([
            'opname_number' => 'OPN-' . date('Ymd') . '-' . rand(1000, 9999),
            'category_id' => $request->category_id,
            'status' => 'in_progress',
            'initiated_by' => auth()->id(),
            'started_at' => now(),
        ]);

        // Populate items with current system qty
        $variantsQuery = ProductVariant::where('is_active', true);
        if ($request->category_id) {
            $variantsQuery->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));
        }

        foreach ($variantsQuery->get() as $variant) {
            StockOpnameItem::create([
                'stock_opname_id' => $opname->id,
                'product_variant_id' => $variant->id,
                'system_qty' => $variant->stock_qty,
                'physical_qty' => null,
                'difference' => 0,
            ]);
        }

        return redirect()->route('inventory.opname.show', $opname)->with('success', 'Stock opname dimulai.');
    }

    public function show(StockOpname $opname)
    {
        $opname->load(['items.productVariant.product', 'initiatedBy', 'approvedBy']);
        return view('inventory.opname.show', compact('opname'));
    }

    public function approve(Request $request, StockOpname $opname)
    {
        if ($opname->status !== 'in_progress') {
            return back()->withErrors(['error' => 'Opname tidak bisa diproses.']);
        }

        // Update physical quantities from request
        foreach ($request->physical_qty ?? [] as $itemId => $qty) {
            $item = StockOpnameItem::find($itemId);
            if ($item && $item->stock_opname_id == $opname->id) {
                $item->update([
                    'physical_qty' => $qty,
                    'difference' => $qty - $item->system_qty,
                ]);

                // Adjust stock
                $variant = $item->productVariant;
                (new StockService())->adjustStock(
                    $variant,
                    $qty,
                    "Stock Opname: {$opname->opname_number}"
                );
            }
        }

        $opname->update([
            'status' => 'completed',
            'approved_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Stock opname berhasil diselesaikan.');
    }
}
