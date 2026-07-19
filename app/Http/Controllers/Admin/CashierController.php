<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierUnit;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class CashierController extends Controller
{
    public function index()
    {
        return $this->monitoring();
    }

    public function monitoring()
    {
        $today = Carbon::today();

        $transactionsToday = Transaction::with('user')
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'voided')
            ->get();

        $units = CashierUnit::with('users')->get();

        $unitSummaries = $units->map(function ($unit) use ($transactionsToday) {
            $userIds = $unit->users->pluck('id');
            $unitTransactions = $transactionsToday->whereIn('user_id', $userIds);

            $latestTransaction = $unitTransactions->sortByDesc('created_at')->first();

            return [
                'unit' => $unit,
                'user' => $latestTransaction?->user?->name ?? $unit->users->first()?->name ?? '-',
                'tx_count' => $unitTransactions->count(),
                'tx_sum' => $unitTransactions->sum('total'),
                'last_at' => $latestTransaction?->created_at,
                'status' => $unitTransactions->count() > 0
                    ? 'Aktif berdasarkan aktivitas transaksi'
                    : 'Tidak ada aktivitas hari ini',
            ];
        });

        $totalTransactions = $unitSummaries->sum('tx_count');
        $totalSales = $unitSummaries->sum('tx_sum');

        $bestUnitSummary = $unitSummaries->sortByDesc('tx_sum')->first();
        $topCashier = $transactionsToday->groupBy('user_id')
            ->map(fn ($transactions) => [
                'user' => $transactions->first()->user?->name ?? 'Kasir',
                'tx_count' => $transactions->count(),
                'tx_sum' => $transactions->sum('total'),
            ])
            ->sortByDesc('tx_count')
            ->first();

        $bestUnitName = optional($bestUnitSummary['unit'] ?? null)->name ?? '-';
        $bestUnitSales = $bestUnitSummary['tx_sum'] ?? 0;

        $topCashierName = $topCashier['user'] ?? '-';
        $topCashierTxCount = $topCashier['tx_count'] ?? 0;

        $salesByUnitLabels = $unitSummaries->pluck('unit.name');
        $salesByUnitData = $unitSummaries->pluck('tx_sum');
        $transactionsByUnitData = $unitSummaries->pluck('tx_count');

        return view('admin.cashiers', compact(
            'unitSummaries',
            'totalTransactions',
            'totalSales',
            'bestUnitName',
            'bestUnitSales',
            'topCashierName',
            'topCashierTxCount',
            'salesByUnitLabels',
            'salesByUnitData',
            'transactionsByUnitData'
        ));
    }
}
