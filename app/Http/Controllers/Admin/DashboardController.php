<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierUnit;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $validTodayQuery = Transaction::whereDate('created_at', $today)
            ->where('status', '!=', 'voided');

        $totalProducts = Product::where('is_active', true)->count();
        $todayTransactions = (clone $validTodayQuery)->count();
        $todaySales = (clone $validTodayQuery)->sum('total');

        $weeklySales = Transaction::where('status', '!=', 'voided')
            ->whereBetween('created_at', [$startOfWeek, $now])
            ->sum('total');

        $monthlySales = Transaction::where('status', '!=', 'voided')
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->sum('total');

        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'stock_min')
            ->count();

        $recentTransactions = Transaction::with('user', 'paymentMethod')
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'stock_min')
            ->orderBy('stock')
            ->take(5)
            ->get();

        $bestSellingProducts = TransactionItem::with('product')
            ->whereHas('transaction', function ($query) {
                $query->where('status', '!=', 'voided');
            })
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $salesChart = collect();

        for ($days = 6; $days >= 0; $days--) {
            $date = Carbon::today()->subDays($days);

            $salesChart->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'total' => Transaction::where('status', '!=', 'voided')
                    ->whereDate('created_at', $date)
                    ->sum('total'),
            ]);
        }

        // Cashier summaries
        $units = CashierUnit::with('users')->get();

        $txStats = Transaction::whereDate('created_at', $today)
            ->where('status', '!=', 'voided')
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as tx_count, SUM(total) as tx_sum')
            ->get()
            ->keyBy('user_id');

        $cashierSummaries = $units->map(function ($unit) use ($txStats) {
            $user = $unit->users->first();
            $stats = $user && isset($txStats[$user->id]) ? $txStats[$user->id] : null;

            return [
                'unit' => $unit,
                'user' => $user,
                'tx_count' => $stats ? $stats->tx_count : 0,
                'tx_sum' => $stats ? $stats->tx_sum : 0,
            ];
        });

        return view('admin.dashboard', compact(
            'totalProducts',
            'todayTransactions',
            'todaySales',
            'weeklySales',
            'monthlySales',
            'lowStockCount',
            'recentTransactions',
            'lowStockProducts',
            'bestSellingProducts',
            'salesChart',
            'cashierSummaries'
        ));
    }
}