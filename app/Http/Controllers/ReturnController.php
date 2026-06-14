<?php

namespace App\Http\Controllers;

use App\Models\ReturnTransaction;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = ReturnTransaction::with(['transaction', 'requestedBy', 'approvedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('returns.index', compact('returns'));
    }

    public function show(ReturnTransaction $return)
    {
        $return->load(['transaction.items.productVariant.product', 'items.productVariant.product', 'requestedBy', 'approvedBy']);
        return view('returns.show', compact('return'));
    }

    public function approve(ReturnTransaction $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Retur ini sudah diproses sebelumnya.');
        }

        $return->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Kembalikan stok untuk setiap item yang diretur
        foreach ($return->items as $item) {
            $variant = $item->productVariant;
            $stockBefore = $variant->stock_qty;
            $variant->increment('stock_qty', $item->quantity);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type'               => 'return',
                'quantity'           => $item->quantity,
                'stock_before'       => $stockBefore,
                'stock_after'        => $stockBefore + $item->quantity,
                'reference_type'     => 'return',
                'reference_id'       => $return->id,
                'notes'              => 'Retur disetujui - #' . $return->return_number,
                'user_id'            => auth()->id(),
            ]);
        }

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'approve_return',
            'description' => 'Menyetujui retur ' . $return->return_number,
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', 'Retur ' . $return->return_number . ' telah disetujui dan stok dikembalikan.');
    }

    public function reject(Request $request, ReturnTransaction $return)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        if ($return->status !== 'pending') {
            return back()->with('error', 'Retur ini sudah diproses sebelumnya.');
        }

        $return->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reason'      => $return->reason . "\n[DITOLAK]: " . $request->reason,
        ]);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'reject_return',
            'description' => 'Menolak retur ' . $return->return_number . ': ' . $request->reason,
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', 'Retur ' . $return->return_number . ' telah ditolak.');
    }
}
