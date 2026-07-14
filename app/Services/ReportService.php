<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Collection;

class ReportService
{
    public function getSalesReport(string $startDate, string $endDate, ?int $userId = null): array
    {
        $query = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->with(['items', 'payments.paymentMethod', 'cashierSession.user']);

        if ($userId) {
            $query->whereHas('cashierSession', fn($q) => $q->where('user_id', $userId));
        }

        $transactions = $query->get();

        $totalRevenue = $transactions->sum('grand_total');
        $totalCogs = $transactions->flatMap->items->sum(fn($i) => $i->buy_price * $i->quantity);
        $totalDiscount = $transactions->sum('discount_amount');
        $grossProfit = $transactions->sum(fn($t) => $t->getGrossProfit());

        // By category
        $byCategory = $transactions->flatMap->items
            ->groupBy(fn($item) => $item->productVariant?->product?->category?->name ?? 'Unknown')
            ->map(fn($items) => [
                'revenue' => $items->sum('subtotal'),
                'qty' => $items->sum('quantity'),
                'count' => $items->count(),
            ]);

        // By payment method
        $byPayment = $transactions->flatMap->payments
            ->groupBy('paymentMethod.name')
            ->map(fn($payments) => $payments->sum('amount'));

        // Daily breakdown
        $daily = $transactions->groupBy(fn($t) => $t->created_at->format('Y-m-d'))
            ->map(fn($group) => [
                'date' => $group->first()->created_at->format('d M Y'),
                'revenue' => $group->sum('grand_total'),
                'count' => $group->count(),
                'profit' => $group->sum(fn($t) => $t->getGrossProfit()),
            ])
            ->sortKeys();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_discount' => $totalDiscount,
            'gross_profit' => $grossProfit,
            'profit_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0,
            'total_transactions' => $transactions->count(),
            'avg_transaction' => $transactions->count() > 0 ? $totalRevenue / $transactions->count() : 0,
            'by_category' => $byCategory,
            'by_payment' => $byPayment,
            'daily' => $daily,
        ];
    }

    public function getProfitLossReport(string $month, string $year): array
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $transactions = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->with('items.productVariant')
            ->get();

        $grossSales = $transactions->sum('subtotal');
        
        // Sum returns
        $returns = \App\Models\ReturnTransaction::whereBetween('approved_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'approved')
            ->sum('total_refund');

        // Sum discounts (manual + promotions + points)
        $discounts = $transactions->sum(fn($t) => $t->discount_amount + $t->promotion_discount + $t->point_discount);
        
        $netSales = $grossSales - $returns - $discounts;
        $cogs = $transactions->flatMap->items->sum(fn($i) => $i->buy_price * $i->quantity);
        $tax = $transactions->sum('tax_amount');
        
        $grossProfit = $netSales - $cogs;

        // Calculate Depreciation (Asset)
        $assets = \App\Models\Asset::where('condition', '!=', 'disposed')->get();
        $depreciation = $assets->sum(fn($a) => $a->purchase_price * (($a->depreciation_rate ?? 0) / 100) / 12);

        // Calculate Shrinkage (Opname loss)
        $opnameItems = \App\Models\StockOpnameItem::whereHas('stockOpname', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('completed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
              ->where('status', 'completed');
        })->where('difference', '<', 0)->with('productVariant')->get();
        
        $shrinkage = $opnameItems->sum(fn($i) => abs($i->difference) * ($i->productVariant?->effective_buy_price ?? 0));

        // Operational Expenses (Biaya operasional)
        $expenses = \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        
        $netProfit = $grossProfit - $depreciation - $shrinkage - $expenses;

        return [
            'period' => date('F Y', strtotime($startDate)),
            'gross_sales' => $grossSales,
            'returns' => $returns,
            'discounts' => $discounts,
            'net_sales' => $netSales,
            'cogs' => $cogs,
            'tax' => $tax,
            'gross_profit' => $grossProfit,
            'depreciation' => $depreciation,
            'shrinkage' => $shrinkage,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'margin_percentage' => $netSales > 0 ? round(($netProfit / $netSales) * 100, 2) : 0,
        ];
    }

    public function getCashierReport(string $startDate, string $endDate): Collection
    {
        return \App\Models\CashierSession::whereBetween('opened_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['user', 'transactions' => fn($q) => $q->where('status', 'completed')])
            ->get()
            ->map(function ($session) {
                return [
                    'kasir' => $session->user->name,
                    'opened_at' => $session->opened_at->format('d M Y H:i'),
                    'closed_at' => $session->closed_at?->format('d M Y H:i') ?? 'Masih Buka',
                    'total_transactions' => $session->transactions->count(),
                    'total_sales' => $session->transactions->sum('grand_total'),
                    'opening_balance' => $session->opening_balance,
                    'closing_balance' => $session->closing_balance,
                    'difference' => $session->difference,
                ];
            });
    }
}
