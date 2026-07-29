<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->input(
            'from',
            Carbon::now()->startOfMonth()->format('Y-m-d')
        );

        $to = $request->input(
            'to',
            Carbon::now()->format('Y-m-d')
        );

        $startDate = Carbon::parse($from)->startOfDay();
        $endDate = Carbon::parse($to)->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $transactions = Transaction::with([
                'user.cashierUnit',
                'paymentMethod',
            ])
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $startDate,
                $endDate,
            ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalSales = $transactions->sum('total');

        $totalTransactions = $transactions->count();

        $transactionItems = TransactionItem::query()
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [
                        $startDate,
                        $endDate,
                    ]);
            })
            ->get();

        $itemsByUnit = $transactionItems
            ->groupBy(function ($item) {
                return $item->product_unit ?? $item->product?->unit ?? 'pcs';
            })
            ->map(function ($items, $unit) {
                return [
                    'unit' => $unit,
                    'qty' => $items->sum('qty'),
                ];
            })
            ->sortBy('unit')
            ->values();

        $totalItems = $itemsByUnit->sum('qty');

        $averageTransaction = $totalTransactions > 0
            ? $totalSales / $totalTransactions
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PROFIT
        |--------------------------------------------------------------------------
        */

        $totalCapital = $transactionItems->sum(function ($item) {
            $buyPrice = $item->buy_price
                ?? $item->product?->buy_price
                ?? 0;

            return (float) $item->qty * (float) $buyPrice;
        });

        $totalProfit = $totalSales - $totalCapital;

        $profitPercentage = $totalCapital > 0
            ? ($totalProfit / $totalCapital) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUMMARY
        |--------------------------------------------------------------------------
        */

        $paymentSummary = $transactions
            ->groupBy(function ($transaction) {
                return $transaction->paymentMethod?->name ?? 'N/A';
            })
            ->map(function ($transactions) {
                return [
                    'total' => $transactions->sum('total'),
                    'count' => $transactions->count(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | CASHIER & UNIT SUMMARY
        |--------------------------------------------------------------------------
        */

        $cashierSummary = $transactions
            ->groupBy('user_id')
            ->map(function ($transactions) {
                $user = $transactions->first()->user;

                return [
                    'user' => $user,
                    'name' => $user?->name ?? 'Kasir tidak diketahui',
                    'unit' => $user?->cashierUnit?->name ?? 'Tanpa Unit',
                    'tx_count' => $transactions->count(),
                    'sales' => $transactions->sum('total'),
                    'average' => $transactions->count() > 0
                        ? $transactions->sum('total') / $transactions->count()
                        : 0,
                    'last_activity' => $transactions->max('created_at'),
                ];
            })
            ->sortByDesc('sales')
            ->values();

        $unitSummary = $transactions
            ->groupBy(fn ($transaction) => $transaction->user?->cashierUnit?->name ?? 'Tanpa Unit')
            ->map(function ($transactions, $unit) {
                $cashiers = $transactions
                    ->pluck('user')
                    ->filter()
                    ->unique('id');

                return [
                    'unit' => $unit,
                    'tx_count' => $transactions->count(),
                    'sales' => $transactions->sum('total'),
                    'cashier_count' => $cashiers->count(),
                    'average' => $transactions->count() > 0
                        ? $transactions->sum('total') / $transactions->count()
                        : 0,
                ];
            })
            ->sortByDesc('sales')
            ->values();

        $cashierActivitySummary = $cashierSummary
            ->sortByDesc('last_activity')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | BEST SELLING PRODUCTS
        |--------------------------------------------------------------------------
        */

        $bestSellingProducts = TransactionItem::query()
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [
                        $startDate,
                        $endDate,
                    ]);
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->product_id ?? ($item->product_name ?? 'deleted');
            })
            ->map(function ($items) {
                $firstItem = $items->first();
                $productName = $firstItem->product_name
                    ?? $firstItem->product?->name
                    ?? 'Produk telah dihapus';

                return [
                    'product' => $firstItem->product,
                    'product_name' => $productName,
                    'product_unit' => $firstItem->product_unit ?? $firstItem->product?->unit ?? 'pcs',
                    'qty' => $items->sum('qty'),
                    'sales' => $items->sum('subtotal'),
                ];
            })
            ->sortByDesc('qty')
            ->take(10)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | SALES CHART
        |--------------------------------------------------------------------------
        */

        $salesChart = Transaction::selectRaw(
                'DATE(created_at) as date, SUM(total) as total'
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $startDate,
                $endDate,
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.sales', compact(
            'transactions',
            'totalSales',
            'totalTransactions',
            'totalItems',
            'averageTransaction',
            'totalCapital',
            'totalProfit',
            'profitPercentage',
            'paymentSummary',
            'cashierSummary',
            'cashierActivitySummary',
            'unitSummary',
            'bestSellingProducts',
            'salesChart',
            'itemsByUnit',
            'from',
            'to'
        ));
    }
}