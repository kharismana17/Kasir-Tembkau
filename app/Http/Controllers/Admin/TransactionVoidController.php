<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionVoid;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionVoidController extends Controller
{
    public function index()
    {
        $requests = TransactionVoid::with('transaction', 'requester', 'reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.voids.index', compact('requests'));
    }

    public function approve(Request $request, TransactionVoid $void)
    {
        if ($void->status !== 'void_requested') {
            return back()->with('error', 'Permintaan tidak dapat diproses.');
        }

        DB::transaction(function () use ($void) {
            // set transaction status to voided
            $transaction = $void->transaction;
            $transaction->status = 'voided';
            $transaction->save();

            // Restore stock for each item and create stock movements
            foreach ($transaction->items as $item) {
                $product = Product::find($item->product_id);
                if (! $product) continue;

                // Determine stock restoration amount (mirror resolveStockReduction)
                if (($product->stock_unit ?? '') === 'gram') {
                    $restore = (int) round($item->qty);
                } elseif ($product->selling_unit === 'gram') {
                    $restore = (int) round($item->qty * 100);
                } else {
                    $restore = (int) round($item->qty);
                }

                $product->increment('stock', $restore);

                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $restore,
                    'type' => 'stock_in',
                    'reference_type' => 'void',
                    'reference_id' => $transaction->id,
                    'user_id' => Auth::id(),
                    'note' => 'Stock restored due to void for transaction ' . $transaction->invoice_no,
                ]);
            }

            // update void record
            $void->status = 'approved';
            $void->reviewed_by = Auth::id();
            $void->reviewed_at = now();
            $void->save();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'approve_void',
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'description' => 'Void approved for ' . $transaction->invoice_no,
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('admin.cashiers.index')->with('success', 'Void transaksi disetujui.');
    }

    public function reject(Request $request, TransactionVoid $void)
    {
        if ($void->status !== 'void_requested') {
            return back()->with('error', 'Permintaan tidak dapat diproses.');
        }

        DB::transaction(function () use ($void) {
            $transaction = $void->transaction;
            // keep transaction as completed (or original status)
            $transaction->status = 'completed';
            $transaction->save();

            $void->status = 'rejected';
            $void->reviewed_by = Auth::id();
            $void->reviewed_at = now();
            $void->save();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'reject_void',
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'description' => 'Void rejected for ' . $transaction->invoice_no,
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('admin.voids.index')->with('success', 'Void transaksi ditolak.');
    }
}
