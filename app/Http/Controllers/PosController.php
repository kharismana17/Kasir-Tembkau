<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('name')
            ->get();

        $cart = session('cart', []);

        $cart = collect($cart)->map(function ($item) {
            $product = Product::find($item['product_id']);

            if ($product) {
                if (! isset($item['unit'])) {
                    $item['unit'] = $product->unit;
                }

                $item['is_tembakau'] = $this->isTembakau($product);
            }

            return $item;
        })->toArray();

        session()->put('cart', $cart);

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        return view('pos.index', compact(
            'products',
            'paymentMethods',
            'cart',
            'subtotal'
        ));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $dailyLimit = config('kasir.daily_transaction_limit', 20);
        $todayTxCount = Transaction::where('user_id', auth()->id())
            ->whereDate('created_at', now())
            ->where('status', 'completed')
            ->count();

        if ($todayTxCount >= $dailyLimit) {
            return back()->with('error', "Batas transaksi harian tercapai. Maksimal {$dailyLimit} transaksi per hari.");
        }

        DB::transaction(function () use ($data, $cart, &$transaction) {
            $subtotal = 0;
            $items = [];

            foreach ($cart as $item) {
                $product = Product::with('category')
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                if (! $product->is_active) {
                    abort(422, "Produk {$product->name} sudah tidak aktif.");
                }

                if (! $this->isValidCartQuantity($product, $item['qty'])) {
                    abort(422, "Jumlah untuk produk {$product->name} tidak valid.");
                }

                $stockReduction = $this->resolveStockReduction($product, $item['qty']);

                if ($product->stock < $stockReduction) {
                    abort(
                        422,
                        "Stok produk {$product->name} tidak mencukupi. Stok saat ini: {$product->stock}. Diperlukan: {$stockReduction}."
                    );
                }

                $itemSubtotal = $product->sell_price * $item['qty'];

                $subtotal += $itemSubtotal;

                $items[] = [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'subtotal' => $itemSubtotal,
                    'stock_reduction' => $stockReduction,
                ];
            }

            // TAX & ROUNDING
            $storeSettings = \App\Models\StoreSetting::first();

            $taxPercentage = $storeSettings->tax_percentage ?? 0;
            $taxAmount = ($taxPercentage > 0) ? round($subtotal * ($taxPercentage / 100), 2) : 0;

            $totalBeforeRound = $subtotal + $taxAmount; // discount assumed 0

            $rounding = (int) ($storeSettings->rounding ?? 0);
            $roundingAmount = 0;
            $total = $totalBeforeRound;

            if ($rounding > 0) {
                $rounded = (float) (round($totalBeforeRound / $rounding) * $rounding);
                $roundingAmount = round($rounded - $totalBeforeRound, 2);
                $total = round($totalBeforeRound + $roundingAmount, 2);
            }

            if ($data['paid_amount'] < $total) {
                abort(422, 'Nominal pembayaran kurang.');
            }

            // Invoice generation using transaction_number_format with {rand}
            $format = $storeSettings->transaction_number_format ?? null;

            if ($format) {
                $replacements = [
                    '{Y}' => now()->format('Y'),
                    '{y}' => now()->format('y'),
                    '{m}' => now()->format('m'),
                    '{d}' => now()->format('d'),
                ];

                $invoiceNo = strtr($format, $replacements);

                if (str_contains($invoiceNo, '{seq}')) {
                    // Treat {seq} as legacy alias for random 4 char
                    $invoiceNo = str_replace('{seq}', Str::upper(Str::random(4)), $invoiceNo);
                }

                if (str_contains($invoiceNo, '{rand}')) {
                    $invoiceNo = str_replace('{rand}', Str::upper(Str::random(4)), $invoiceNo);
                }
            } else {
                $invoiceNo = 'TRX-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
            }

            $transaction = Transaction::create([
                'invoice_no' => $invoiceNo,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total_before_round' => $totalBeforeRound,
                'rounding' => $rounding,
                'rounding_amount' => $roundingAmount,
                'total' => $total,
                'payment_method_id' => $data['payment_method_id'],
                'paid_amount' => $data['paid_amount'],
                'change_amount' => $data['paid_amount'] - $total,
                'status' => 'completed',
            ]);

            // Audit log: create transaction
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_transaction',
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'description' => 'Transaction created ' . $transaction->invoice_no,
                'ip_address' => request()->ip(),
            ]);

            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'qty' => $item['qty'],
                    'price' => $item['product']->sell_price,
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                ]);

                $item['product']->decrement(
                    'stock',
                    $item['stock_reduction']
                );

                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'change' => -$item['stock_reduction'],
                    'type' => 'stock_out',
                    'reference_type' => 'transaction',
                    'reference_id' => $transaction->id,
                    'user_id' => auth()->id(),
                    'note' => 'Stok keluar akibat transaksi ' . $transaction->invoice_no,
                ]);
            }
        });

        session()->forget('cart');

        return redirect()
            ->route('pos.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function addToCart(Product $product)
    {
        if (! $product->is_active || $product->stock <= 0) {
            return back()->with('error', 'Produk tidak tersedia.');
        }
        $cart = session('cart', []);

        $requestedQty = request()->input('qty');

        // If qty provided (e.g., weight in grams), use it; otherwise default increment/add 1
        if ($requestedQty !== null) {
            $qty = $requestedQty + 0;
        } else {
            $qty = null;
        }

        // Determine unit to store in cart: prefer stock unit for tembakau
        $unit = $product->stockUnit();

        if (isset($cart[$product->id])) {
            if ($qty === null) {
                $newQty = $cart[$product->id]['qty'] + 1;
            } else {
                $newQty = $cart[$product->id]['qty'] + $qty;
            }

            $requiredStock = $this->resolveStockReduction($product, $newQty);

            if ($requiredStock > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok tersedia.');
            }

            $cart[$product->id]['qty'] = $newQty;
            $cart[$product->id]['unit'] = $unit;
            $cart[$product->id]['is_tembakau'] = $this->isTembakau($product);
        } else {
            $initialQty = $qty === null ? 1 : $qty;

            $requiredStock = $this->resolveStockReduction($product, $initialQty);

            if ($requiredStock > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok tersedia.');
            }

            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sell_price,
                'qty' => $initialQty,
                'unit' => $unit,
                'is_tembakau' => $this->isTembakau($product),
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, Product $product)
    {
        if ($this->isTembakau($product)) {
            $data = $request->validate([
                'qty' => ['required', 'numeric', 'gt:0'],
            ]);
        } else {
            $data = $request->validate([
                'qty' => ['required', 'integer', 'min:1'],
            ]);
        }

        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ada di keranjang.');
        }

        $requiredStock = $this->resolveStockReduction($product, $data['qty']);

        if ($requiredStock > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        $cart[$product->id]['qty'] = $data['qty'];

        session()->put('cart', $cart);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    public function removeFromCart(Product $product)
    {
        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ada di keranjang.');
        }

        unset($cart[$product->id]);

        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clearCart()
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    protected function resolveStockReduction(Product $product, float $qty): int
    {
        // If product stock unit is gram, qty is provided in grams => reduce directly
        if (($product->stock_unit ?? '') === 'gram') {
            return (int) round($qty);
        }

        // Backward compatibility: if selling unit is ons (qty in ons) => convert to gram
        if ($product->selling_unit === 'ons') {
            return (int) round($qty * 100);
        }

        // For other units (pcs, pack), 1-to-1 conversion
        return (int) round($qty);
    }

    protected function isValidCartQuantity(Product $product, $qty): bool
    {
        if (! is_numeric($qty) || $qty <= 0) {
            return false;
        }

        // If stock unit is gram (tembakau), allow numeric (grams)
        if (($product->stock_unit ?? '') === 'gram') {
            return true;
        }

        if ($product->selling_unit === 'ons') {
            return true;
        }

        return is_int($qty + 0) || floor($qty) == $qty;
    }

    protected function isTembakau(Product $product): bool
    {
        return $product->selling_unit === 'ons';
    }

    public function scanBarcode(Request $request)
    {
        $barcode = $request->query('barcode');

        if (! $barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak boleh kosong.',
            ], 400);
        }

        $product = Product::where('barcode', $barcode)
            ->with('category')
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk dengan barcode ini tidak ditemukan.',
            ], 404);
        }

        if (! $product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak aktif.',
            ], 422);
        }

        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok produk tidak tersedia.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => $product->sell_price,
                'stock' => $product->stock,
                'selling_unit' => $product->selling_unit,
                'is_tembakau' => $this->isTembakau($product),
            ],
        ]);
    }

    public function transactions()
    {
        $transactions = Transaction::with('items', 'paymentMethod')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pos.transactions', compact('transactions'));
    }

    public function requestVoid(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk membatalkan transaksi ini.');
        }

        if ($transaction->status !== 'completed') {
            return back()->with('error', 'Transaksi tidak dapat dibatalkan.');
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $void = \App\Models\TransactionVoid::create([
            'transaction_id' => $transaction->id,
            'requested_by' => auth()->id(),
            'reason' => $data['reason'] ?? null,
            'status' => 'void_requested',
        ]);

        // Audit log: request void
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'request_void',
            'auditable_type' => Transaction::class,
            'auditable_id' => $transaction->id,
            'description' => 'Void requested: ' . ($data['reason'] ?? ''),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Permintaan void terkirim. Menunggu persetujuan admin.');
    }
}