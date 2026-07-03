<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'creator'])
            ->latest()
            ->paginate(15);
            
        return view('inventory.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        // Get all products with variants for the transfer form selector
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->get();
            
        return view('inventory.transfers.create', compact('branches', 'variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id|different:to_branch_id',
            'to_branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $transferNumber = 'TRF-' . date('Ymd') . '-' . rand(1000, 9999);

            $transfer = StockTransfer::create([
                'transfer_number' => $transferNumber,
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $request->to_branch_id,
                'status' => 'pending', // Directly goes to pending approval
                'created_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return redirect()->route('inventory.transfers.index')
                ->with('success', 'Mutasi stok #' . $transferNumber . ' berhasil diajukan & menunggu persetujuan.');
        });
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load(['fromBranch', 'toBranch', 'creator', 'approver', 'items.productVariant.product']);
        return view('inventory.transfers.show', compact('transfer'));
    }

    public function update(Request $request, StockTransfer $transfer)
    {
        // Approve action
        if ($request->action === 'approve') {
            if ($transfer->status !== 'pending') {
                return back()->with('error', 'Hanya transfer berstatus PENDING yang bisa disetujui.');
            }

            return DB::transaction(function () use ($transfer) {
                // Check stock availability in from_branch
                foreach ($transfer->items as $item) {
                    $fromStock = BranchProductStock::where('branch_id', $transfer->from_branch_id)
                        ->where('product_variant_id', $item->product_variant_id)
                        ->first();

                    if (!$fromStock || $fromStock->stock_qty < $item->quantity) {
                        $variantName = $item->productVariant->product->name . ' (' . $item->productVariant->variant_label . ')';
                        throw new \Exception('Stok cabang asal tidak mencukupi untuk item: ' . $variantName);
                    }
                }

                // Deduct from from_branch & Add to to_branch
                foreach ($transfer->items as $item) {
                    // From
                    $fromStock = BranchProductStock::where('branch_id', $transfer->from_branch_id)
                        ->where('product_variant_id', $item->product_variant_id)
                        ->first();
                    $fromStock->decrement('stock_qty', $item->quantity);

                    // To
                    $toStock = BranchProductStock::firstOrCreate(
                        ['branch_id' => $transfer->to_branch_id, 'product_variant_id' => $item->product_variant_id],
                        ['stock_qty' => 0]
                    );
                    $toStock->increment('stock_qty', $item->quantity);
                }

                $transfer->update([
                    'status' => 'completed',
                    'approved_by' => auth()->id(),
                ]);

                return redirect()->route('inventory.transfers.show', $transfer)
                    ->with('success', 'Mutasi stok berhasil disetujui dan stok telah dipindahkan.');
            });
        }

        // Cancel action
        if ($request->action === 'cancel') {
            if ($transfer->status !== 'pending') {
                return back()->with('error', 'Hanya transfer berstatus PENDING yang bisa dibatalkan.');
            }

            $transfer->update(['status' => 'cancelled']);
            
            return redirect()->route('inventory.transfers.show', $transfer)
                ->with('success', 'Mutasi stok dibatalkan.');
        }

        return back();
    }
}
