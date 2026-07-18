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
                'user',
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

        $transactionItems = TransactionItem::with('product')
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
                return $item->product?->unit ?: 'pcs';
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
            return $item->qty * $item->product->buy_price;
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
        | BEST SELLING PRODUCTS
        |--------------------------------------------------------------------------
        */

        $bestSellingProducts = TransactionItem::with('product')
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [
                        $startDate,
                        $endDate,
                    ]);
            })
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product' => $items->first()->product,
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
            'bestSellingProducts',
            'salesChart',
            'itemsByUnit',
            'from',
            'to'
        ));
    }
}