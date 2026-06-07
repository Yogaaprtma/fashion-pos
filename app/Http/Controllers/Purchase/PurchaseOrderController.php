<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceivedNote;
use App\Models\GrnItem;
use App\Models\Supplier;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = PurchaseOrder::with('supplier')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();
        return view('purchase.orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $variants = ProductVariant::with('product')->where('is_active', true)->get();
        return view('purchase.orders.create', compact('suppliers', 'variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $poNumber = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);
        $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity_ordered'] * $i['unit_price']);

        $po = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_id' => $request->supplier_id,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'ordered_by' => auth()->id(),
            'notes' => $request->notes,
            'expected_date' => $request->expected_date,
        ]);

        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_variant_id' => $item['product_variant_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'quantity_received' => 0,
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity_ordered'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('purchase.orders.show', $po)->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $order)
    {
        $order->load(['supplier', 'items.productVariant.product', 'orderedBy', 'goodsReceivedNotes.receivedBy']);
        return view('purchase.orders.show', compact('order'));
    }

    public function edit(PurchaseOrder $order)
    {
        if (!in_array($order->status, ['draft'])) {
            return back()->withErrors(['error' => 'Hanya PO berstatus Draft yang bisa diedit.']);
        }
        $suppliers = Supplier::where('is_active', true)->get();
        $order->load(['supplier', 'items.productVariant.product']);
        return view('purchase.orders.edit', compact('order', 'suppliers'));
    }

    public function update(Request $request, PurchaseOrder $order)
    {
        $order->update($request->only(['supplier_id', 'notes', 'expected_date', 'status']));
        return redirect()->route('purchase.orders.show', $order)->with('success', 'PO diperbarui.');
    }

    public function destroy(PurchaseOrder $order)
    {
        if ($order->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya PO Draft yang bisa dihapus.']);
        }
        $order->delete();
        return redirect()->route('purchase.orders.index')->with('success', 'PO dihapus.');
    }

    public function receive(Request $request, PurchaseOrder $order)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        $grn = GoodsReceivedNote::create([
            'grn_number' => 'GRN-' . date('Ymd') . '-' . rand(1000, 9999),
            'purchase_order_id' => $order->id,
            'received_by' => auth()->id(),
            'received_at' => now(),
            'notes' => $request->notes,
        ]);

        $allReceived = true;
        $anyReceived = false;

        foreach ($request->items as $item) {
            $poItem = $order->items()->find($item['po_item_id']);
            if (!$poItem || $item['quantity_received'] == 0) continue;

            GrnItem::create([
                'grn_id' => $grn->id,
                'purchase_order_item_id' => $poItem->id,
                'product_variant_id' => $poItem->product_variant_id,
                'quantity_received' => $item['quantity_received'],
            ]);

            $poItem->increment('quantity_received', $item['quantity_received']);

            // Add to stock
            (new StockService())->addStock(
                $poItem->productVariant,
                $item['quantity_received'],
                'purchase_order',
                $order->id,
                "Terima barang PO: {$order->po_number}"
            );

            $anyReceived = true;
            if ($poItem->quantity_received < $poItem->quantity_ordered) {
                $allReceived = false;
            }
        }

        $order->update(['status' => $allReceived ? 'received' : 'partial']);

        return redirect()->route('purchase.orders.show', $order)->with('success', 'Barang berhasil diterima & stok diperbarui.');
    }
}
