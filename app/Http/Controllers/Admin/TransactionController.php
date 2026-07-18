<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'paymentMethod'])
            ->latest()
            ->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load([
            'user',
            'paymentMethod',
            'items.product',
        ]);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load([
            'user',
            'paymentMethod',
            'items.product',
        ]);

        $storeSetting = StoreSetting::first();

        return view('admin.transactions.receipt', compact('transaction', 'storeSetting'));
    }

    public function report(Request $request)
    {
        $from = $request->input(
            'from',
            Carbon::today()->startOfMonth()->toDateString()
        );

        $to = $request->input(
            'to',
            Carbon::today()->toDateString()
        );

        $query = Transaction::with([
            'user',
            'paymentMethod',
            'items.product',
        ])
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);

        $transactions = $query
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Penjualan
        |--------------------------------------------------------------------------
        */

        $totalTransactions = $transactions->count();

        $totalSales = $transactions->sum('total');

        $totalItems = $transactions
            ->flatMap(fn ($transaction) => $transaction->items)
            ->sum('qty');

        $averageTransaction = $totalTransactions > 0
            ? $totalSales / $totalTransactions
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Metode Pembayaran
        |--------------------------------------------------------------------------
        */

        $paymentSummary = $transactions
            ->groupBy(fn ($transaction) =>
                $transaction->paymentMethod?->name ?? 'Tidak diketahui'
            )
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('total'),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Produk Terlaris
        |--------------------------------------------------------------------------
        */

        $bestSellingProducts = $transactions
            ->flatMap(fn ($transaction) => $transaction->items)
            ->groupBy('product_id')
            ->map(function ($items) {

                $product = $items->first()->product;

                $qty = $items->sum('qty');

                $sales = $items->sum(function ($item) {
                    return $item->price * $item->qty;
                });

                return [
                    'product' => $product,
                    'qty' => $qty,
                    'sales' => $sales,
                ];
            })
            ->sortByDesc('qty')
            ->values()
            ->take(10);

        /*
        |--------------------------------------------------------------------------
        | Perhitungan Keuntungan
        |--------------------------------------------------------------------------
        */

        $totalCapital = $transactions
            ->flatMap(fn ($transaction) => $transaction->items)
            ->sum(function ($item) {
                return ($item->product?->buy_price ?? 0) * $item->qty;
            });

        $totalProfit = $totalSales - $totalCapital;

        $profitPercentage = $totalCapital > 0
            ? ($totalProfit / $totalCapital) * 100
            : 0;

        return view('admin.reports.sales', compact(
            'from',
            'to',
            'transactions',
            'totalTransactions',
            'totalSales',
            'totalItems',
            'averageTransaction',
            'paymentSummary',
            'bestSellingProducts',
            'totalCapital',
            'totalProfit',
            'profitPercentage'
        ));
    }
}