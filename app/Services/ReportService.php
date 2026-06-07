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
            ->with('items')
            ->get();

        $revenue = $transactions->sum('grand_total');
        $cogs = $transactions->flatMap->items->sum(fn($i) => $i->buy_price * $i->quantity);
        $discount = $transactions->sum('discount_amount');
        $tax = $transactions->sum('tax_amount');
        $grossProfit = $revenue - $cogs - $discount;

        return [
            'period' => date('F Y', strtotime($startDate)),
            'revenue' => $revenue,
            'cogs' => $cogs,
            'discount' => $discount,
            'tax' => $tax,
            'gross_profit' => $grossProfit,
            'profit_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
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
