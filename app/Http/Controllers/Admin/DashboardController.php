<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalProducts = Product::where('is_active', true)->count();
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $todaySales = Transaction::whereDate('created_at', $today)->sum('total');
        $lowStockCount = Product::whereColumn('stock', '<=', 'stock_min')->count();

        $recentTransactions = Transaction::with('user', 'paymentMethod')
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_min')
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'todayTransactions',
            'todaySales',
            'lowStockCount',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}
