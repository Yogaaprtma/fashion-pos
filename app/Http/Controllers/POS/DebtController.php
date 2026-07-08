<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use App\Models\PaymentMethod;
use App\Models\CustomerDebtPayment;
use App\Models\SupplierDebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    // ===================================================
    // CUSTOMER DEBT (PIUTANG / KASBON)
    // ===================================================
    public function customersIndex()
    {
        $transactions = Transaction::with('customer')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest()
            ->paginate(15);

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->where('type', '!=', 'tempo')
            ->get();

        return view('pos.debts.customers', compact('transactions', 'paymentMethods'));
    }

    public function payCustomerDebt(Request $request, Transaction $transaction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $transaction->remaining_debt,
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $transaction) {
            $amount = $request->amount;

            // Create Debt Payment Record
            CustomerDebtPayment::create([
                'customer_id' => $transaction->customer_id,
                'transaction_id' => $transaction->id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $amount,
                'payment_date' => now(),
                'notes' => $request->notes,
                'received_by' => auth()->id(),
            ]);

            // Update Transaction Debt
            $newRemaining = $transaction->remaining_debt - $amount;
            $transaction->update([
                'remaining_debt' => $newRemaining,
                'paid_amount' => $transaction->paid_amount + $amount,
                'payment_status' => $newRemaining <= 0 ? 'paid' : 'partial',
            ]);

            return redirect()->route('customers.debts.customers.index')
                ->with('success', 'Pembayaran kasbon sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat.');
        });
    }

    // ===================================================
    // SUPPLIER DEBT (HUTANG SUPPLIER)
    // ===================================================
    public function suppliersIndex()
    {
        $orders = PurchaseOrder::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest()
            ->paginate(15);

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->where('type', '!=', 'tempo')
            ->get();

        return view('pos.debts.suppliers', compact('orders', 'paymentMethods'));
    }

    public function paySupplierDebt(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $purchaseOrder->remaining_debt,
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $purchaseOrder) {
            $amount = $request->amount;

            // Create Supplier Debt Payment Record
            SupplierDebtPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $amount,
                'payment_date' => now(),
                'notes' => $request->notes,
                'paid_by' => auth()->id(),
            ]);

            // Update PO Debt
            $newRemaining = $purchaseOrder->remaining_debt - $amount;
            $purchaseOrder->update([
                'remaining_debt' => $newRemaining,
                'payment_status' => $newRemaining <= 0 ? 'paid' : 'partial',
            ]);

            return redirect()->route('purchase.debts.suppliers.index')
                ->with('success', 'Pembayaran hutang supplier sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat.');
        });
    }
}
