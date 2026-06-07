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
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil.',
                'transaction' => $transaction,
                'invoice_number' => $transaction->invoice_number,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
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
