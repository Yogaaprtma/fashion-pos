<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\CashierSession;
use App\Models\StoreSetting;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerPointHistory;
use App\Models\Promotion;
use App\Models\BranchProductStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionService
{
    public function createTransaction(array $data, CashierSession $session): Transaction
    {
        return DB::transaction(function () use ($data, $session) {
            $invoiceNumber = $this->generateInvoiceNumber();

            // Calculate tax
            $taxEnabled = StoreSetting::get('tax_enabled', '0') == '1';
            $taxPercent = $taxEnabled ? (float) StoreSetting::get('tax_percent', '11') : 0;
            $taxAmount = 0;

            $subtotal = collect($data['items'])->sum(function ($item) {
                return ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0);
            });

            $discountAmount = $data['discount_amount'] ?? 0;
            $discountPercent = $data['discount_percent'] ?? 0;
            $promotionId = $data['promotion_id'] ?? null;
            $promotionDiscount = $data['promotion_discount'] ?? 0;
            $pointsUsed = $data['points_used'] ?? 0;
            $pointDiscount = $data['point_discount'] ?? 0;

            $afterDiscount = $subtotal - $discountAmount - $promotionDiscount - $pointDiscount;
            $afterDiscount = max(0, $afterDiscount);
            if ($taxPercent > 0) {
                $taxAmount = $afterDiscount * ($taxPercent / 100);
            }

            $grandTotal = $afterDiscount + $taxAmount;
            $paidAmount = collect($data['payments'])->sum('amount');
            
            $hasTempo = false;
            foreach ($data['payments'] as $payment) {
                $pm = \App\Models\PaymentMethod::find($payment['payment_method_id']);
                if ($pm && $pm->type === 'tempo') {
                    $hasTempo = true;
                }
            }

            if ($hasTempo) {
                if (empty($data['customer_id'])) {
                    throw new \Exception('Metode pembayaran Tempo/Kasbon memerlukan pelanggan (member) yang terdaftar!');
                }
                $changeAmount = 0;
                $remainingDebt = max(0, $grandTotal - $paidAmount);
                $paymentStatus = $paidAmount > 0 ? 'partial' : 'unpaid';
                $dueDate = now()->addDays(30); // Default 30 days
            } else {
                $changeAmount = max(0, $paidAmount - $grandTotal);
                $remainingDebt = 0;
                $paymentStatus = 'paid';
                $dueDate = null;
            }

            // Create transaction
            $transaction = Transaction::create([
                'cashier_session_id' => $session->id,
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percent' => $discountPercent,
                'promotion_id' => $promotionId,
                'promotion_discount' => $promotionDiscount,
                'points_used' => $pointsUsed,
                'point_discount' => $pointDiscount,
                'points_earned' => 0,
                'tax_amount' => $taxAmount,
                'tax_percent' => $taxPercent,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'status' => 'completed',
                'payment_status' => $paymentStatus,
                'remaining_debt' => $remainingDebt,
                'due_date' => $dueDate,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create items and deduct stock
            foreach ($data['items'] as $item) {
                $variant = ProductVariant::with('product')->find($item['product_variant_id']);
                $discountItem = $item['discount_amount'] ?? 0;
                $itemSubtotal = ($item['unit_price'] * $item['quantity']) - $discountItem;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_info' => $variant->variant_label,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'buy_price' => $variant->effective_buy_price,
                    'discount_amount' => $discountItem,
                    'subtotal' => $itemSubtotal,
                ]);

                // Deduct stock
                $branchId = $session->branch_id;
                if ($branchId) {
                    $branchStock = BranchProductStock::firstOrCreate(
                        ['branch_id' => $branchId, 'product_variant_id' => $variant->id],
                        ['stock_qty' => $variant->stock_qty]
                    );
                    $branchStock->decrement('stock_qty', $item['quantity']);
                }

                $stockBefore = $variant->stock_qty;
                $variant->decrement('stock_qty', $item['quantity']);
                $variant->refresh();

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'out',
                    'quantity' => -$item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $variant->stock_qty,
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'user_id' => auth()->id(),
                ]);
            }

            // Create payments
            foreach ($data['payments'] as $payment) {
                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                    'reference_number' => $payment['reference_number'] ?? null,
                ]);
            }

            // Update session totals
            $session->increment('total_transactions');
            $session->increment('total_sales', $grandTotal);

            // Update customer if exists
            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customer->increment('total_spent', $grandTotal);

                    if ($pointsUsed > 0) {
                        $customer->decrement('points', $pointsUsed);
                        CustomerPointHistory::create([
                            'customer_id' => $customer->id,
                            'transaction_id' => $transaction->id,
                            'amount' => -$pointsUsed,
                            'type' => 'redeem',
                            'description' => 'Redeem points for transaction ' . $invoiceNumber,
                        ]);
                    }

                    // 1 point for every 10,000
                    $pointsEarned = floor($grandTotal / 10000);
                    if ($pointsEarned > 0) {
                        $customer->increment('points', $pointsEarned);
                        $transaction->update(['points_earned' => $pointsEarned]);
                        CustomerPointHistory::create([
                            'customer_id' => $customer->id,
                            'transaction_id' => $transaction->id,
                            'amount' => $pointsEarned,
                            'type' => 'earn',
                            'description' => 'Earn points from transaction ' . $invoiceNumber,
                        ]);
                    }
                }
            }

            if ($promotionId) {
                $promo = Promotion::find($promotionId);
                if ($promo) {
                    $promo->increment('used_count');
                }
            }

            AuditLog::record('create_transaction', $transaction, [], ['invoice' => $invoiceNumber, 'total' => $grandTotal]);

            return $transaction->load(['items', 'payments.paymentMethod', 'cashierSession.user']);
        });
    }

    public function voidTransaction(Transaction $transaction, string $reason): Transaction
    {
        return DB::transaction(function () use ($transaction, $reason) {
            if ($transaction->status !== 'completed') {
                throw new \Exception('Hanya transaksi selesai yang bisa di-void.');
            }

            $transaction->update([
                'status' => 'voided',
                'voided_by' => auth()->id(),
                'voided_at' => now(),
                'void_reason' => $reason,
            ]);

            // Restore stock
            foreach ($transaction->items as $item) {
                $variant = $item->productVariant;
                $stockBefore = $variant->stock_qty;
                $variant->increment('stock_qty', $item->quantity);
                $variant->refresh();

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'adjustment',
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $variant->stock_qty,
                    'reference_type' => 'void_transaction',
                    'reference_id' => $transaction->id,
                    'notes' => 'Void Transaksi: ' . $transaction->invoice_number,
                    'user_id' => auth()->id(),
                ]);
            }

            // Update session totals
            $transaction->cashierSession->decrement('total_transactions');
            $transaction->cashierSession->decrement('total_sales', $transaction->grand_total);

            AuditLog::record('void_transaction', $transaction, [], ['reason' => $reason]);

            return $transaction->fresh();
        });
    }

    public function processReturn(Transaction $transaction, array $returnData): \App\Models\ReturnTransaction
    {
        return DB::transaction(function () use ($transaction, $returnData) {
            $returnNumber = 'RET-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $totalRefund = 0;
            $itemsToReturn = [];

            foreach ($returnData['items'] as $itemId => $qty) {
                if ($qty <= 0) continue;

                $item = $transaction->items()->find($itemId);
                if (!$item) continue;

                $refundAmount = ($item->subtotal / $item->quantity) * $qty;
                $totalRefund += $refundAmount;

                $itemsToReturn[] = [
                    'transaction_item_id' => $item->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $qty,
                    'refund_amount' => $refundAmount,
                ];
            }

            if (empty($itemsToReturn)) {
                throw new \Exception('Tidak ada item yang dipilih untuk diretur.');
            }

            $returnTrx = \App\Models\ReturnTransaction::create([
                'transaction_id' => $transaction->id,
                'return_number' => $returnNumber,
                'total_refund' => $totalRefund,
                'reason' => $returnData['reason'],
                'status' => 'approved', // Auto approve for demo
                'requested_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            foreach ($itemsToReturn as $rtItem) {
                \App\Models\ReturnItem::create([
                    'return_id' => $returnTrx->id,
                    'transaction_item_id' => $rtItem['transaction_item_id'],
                    'product_variant_id' => $rtItem['product_variant_id'],
                    'quantity' => $rtItem['quantity'],
                    'refund_amount' => $rtItem['refund_amount'],
                ]);

                // Restore stock
                $variant = ProductVariant::find($rtItem['product_variant_id']);
                $stockBefore = $variant->stock_qty;
                $variant->increment('stock_qty', $rtItem['quantity']);

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'return',
                    'quantity' => $rtItem['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $variant->stock_qty,
                    'reference_type' => 'return',
                    'reference_id' => $returnTrx->id,
                    'notes' => 'Retur Barang dari transaksi: ' . $transaction->invoice_number,
                    'user_id' => auth()->id(),
                ]);
            }

            $transaction->update(['status' => 'partial_return']);

            // Adjust session total sales slightly (optional, depending on accounting rules)
            // $transaction->cashierSession->decrement('total_sales', $totalRefund);

            AuditLog::record('return_transaction', $transaction, [], ['return_number' => $returnNumber, 'total_refund' => $totalRefund]);

            return $returnTrx;
        });
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd');
        $lastInvoice = Transaction::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $sequence = (int) end($parts) + 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getDailySummary(?string $date = null): array
    {
        $date = $date ?? today();

        $transactions = Transaction::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->with(['items', 'payments.paymentMethod'])
            ->get();

        $totalRevenue = $transactions->sum('grand_total');
        $totalProfit = $transactions->sum(fn($t) => $t->getGrossProfit());
        $totalTransactions = $transactions->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $paymentBreakdown = $transactions->flatMap->payments
            ->groupBy('paymentMethod.name')
            ->map(fn($payments) => $payments->sum('amount'));

        return [
            'date' => $date,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
            'total_transactions' => $totalTransactions,
            'avg_transaction_value' => $avgTransactionValue,
            'payment_breakdown' => $paymentBreakdown,
        ];
    }
}
