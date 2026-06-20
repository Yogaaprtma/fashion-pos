<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CashierSession;
use App\Models\StoreSetting;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        $session = auth()->user()->activeSession();
        if (!$session) {
            return response()->json(['error' => 'Tidak ada sesi kasir aktif.'], 422);
        }

        try {
            $transaction = $this->transactionService->createTransaction($request->all(), $session);
            $transaction->load('customer');
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil.',
                'transaction' => $transaction,
                'invoice_number' => $transaction->invoice_number,
                'customer_phone' => $transaction->customer?->phone,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function whatsapp(Transaction $transaction)
    {
        $transaction->load(['items.productVariant.product', 'payments.paymentMethod', 'cashierSession.user', 'customer']);
        $storeName = StoreSetting::get('store_name', 'FashionPOS');
        $phone = $transaction->customer?->phone;

        if (!$phone) {
            abort(404, 'Pelanggan tidak memiliki nomor WA.');
        }

        // Format phone: remove leading 0, add 62
        $phone = preg_replace('/^0/', '62', $phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $itemLines = $transaction->items->map(function ($item) {
            return "- {$item->product_name} ({$item->variant_info}) x{$item->quantity} = Rp " . number_format($item->subtotal, 0, ',', '.');
        })->join("\n");

        $message = "🧾 *STRUK BELANJA - {$storeName}*\n";
        $message .= "================================\n";
        $message .= "No. Invoice: *{$transaction->invoice_number}*\n";
        $message .= "Tanggal: " . $transaction->created_at->format('d/m/Y H:i') . "\n";
        $message .= "================================\n";
        $message .= $itemLines . "\n";
        $message .= "================================\n";
        if ($transaction->discount_amount > 0) {
            $message .= "Diskon: -Rp " . number_format($transaction->discount_amount, 0, ',', '.') . "\n";
        }
        if ($transaction->tax_amount > 0) {
            $message .= "Pajak: Rp " . number_format($transaction->tax_amount, 0, ',', '.') . "\n";
        }
        $message .= "*TOTAL: Rp " . number_format($transaction->grand_total, 0, ',', '.') . "*\n";
        $message .= "Pembayaran: " . $transaction->payments->map(fn($p) => $p->paymentMethod->name . ' Rp ' . number_format($p->amount, 0, ',', '.'))->join(', ') . "\n";
        if ($transaction->change_amount > 0) {
            $message .= "Kembalian: Rp " . number_format($transaction->change_amount, 0, ',', '.') . "\n";
        }
        $message .= "================================\n";
        $message .= "Terima kasih atas kunjungan Anda! 🙏";

        $waUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

        return redirect()->away($waUrl);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.productVariant.product', 'payments.paymentMethod', 'cashierSession.user']);
        return view('pos.transaction-detail', compact('transaction'));
    }

    public function void(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        // Only supervisor and admin can void
        if (!auth()->user()->hasAnyRole(['admin', 'supervisor'])) {
            return response()->json(['error' => 'Tidak memiliki akses untuk void transaksi.'], 403);
        }

        try {
            $this->transactionService->voidTransaction($transaction, $request->reason);
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil di-void.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function processReturn(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
            'return_items' => 'required|array',
        ]);

        try {
            $this->transactionService->processReturn($transaction, [
                'items' => $request->return_items,
                'reason' => $request->reason
            ]);
            return redirect()->route('pos.transaction.show', $transaction)
                ->with('success', 'Retur barang berhasil diproses.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items.productVariant.product', 'payments.paymentMethod', 'cashierSession.user']);
        $storeSettings = StoreSetting::getGroup('general');
        $receiptSettings = StoreSetting::getGroup('receipt');
        return view('pos.receipt', compact('transaction', 'storeSettings', 'receiptSettings'));
    }

    public function receiptPdf(Transaction $transaction)
    {
        $transaction->load(['items.productVariant.product', 'payments.paymentMethod', 'cashierSession.user']);
        $storeSettings = StoreSetting::getGroup('general');
        $pdf = Pdf::loadView('pos.receipt-pdf', compact('transaction', 'storeSettings'))
            ->setPaper([0, 0, 226, 800], 'portrait'); // 80mm thermal
        return $pdf->stream('struk-' . $transaction->invoice_number . '.pdf');
    }

    public function hold(Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return response()->json(['error' => 'Transaksi tidak bisa ditahan.'], 422);
        }
        $transaction->update(['status' => 'held']);
        return response()->json(['success' => true, 'message' => 'Transaksi ditahan.']);
    }

    public function recall(Transaction $transaction)
    {
        $session = auth()->user()->activeSession();
        if (!$session) {
            return response()->json(['error' => 'Tidak ada sesi kasir aktif.'], 422);
        }
        if ($transaction->status !== 'held') {
            return response()->json(['error' => 'Transaksi tidak dalam status ditahan.'], 422);
        }
        $transaction->update(['status' => 'pending']);
        $transaction->load(['items.productVariant.product']);
        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    public function heldList()
    {
        $session = auth()->user()->activeSession();
        $heldTransactions = [];
        if ($session) {
            $heldTransactions = Transaction::where('cashier_session_id', $session->id)
                ->where('status', 'held')
                ->with(['items.productVariant.product'])
                ->get();
        }
        return response()->json($heldTransactions);
    }

    public function history(Request $request)
    {
        $query = Transaction::with(['cashierSession.user', 'payments.paymentMethod'])
            ->latest();

        if ($request->search) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('pos.history', compact('transactions'));
    }
}
