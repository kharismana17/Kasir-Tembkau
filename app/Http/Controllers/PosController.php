<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\AuditLog;


class PosController extends Controller
{
    /**
     * Display POS page.
     */
    public function index()
    {
        $products = $this->getActiveProducts();
        $paymentMethods = $this->getPaymentMethods();
        $cart = $this->getCart();
        $summary = $this->calculateCartSummary($cart);
        $cartCount = count($cart);

        return view('pos.index', [
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'cart' => $cart,
            'subtotal' => $summary['subtotal'],
            'taxAmount' => $summary['taxAmount'],
            'discount' => $summary['discount'],
            'grandTotal' => $summary['grandTotal'],
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Display checkout page.
     */
    public function checkoutPage()
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('pos.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $paymentMethods = $this->getPaymentMethods();
        $summary = $this->calculateCartSummary($cart);

        return view('pos.checkout', [
            'cart' => $cart,
            'paymentMethods' => $paymentMethods,
            'subtotal' => $summary['subtotal'],
            'discount' => $summary['discount'],
            'taxAmount' => $summary['taxAmount'],
            'grandTotal' => $summary['grandTotal'],
        ]);
    }

    public function receiptPage(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load(['items.product', 'paymentMethod', 'user']);

        return view('pos.receipt', compact('transaction'));
    }

    /**
     * Save cart items.
     */
    public function save(Request $request)
    {
        return back()->with('success', 'Transaksi berhasil disimpan.');
    }
    

    /**
     * Process checkout and persist transaction.
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
        $isQris = str_contains(strtolower($paymentMethod->name), 'qris');
        $cart = $this->getCart();

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

        DB::transaction(function () use ($data, $cart, $paymentMethod, $isQris, &$transaction) {
            $items = collect($cart)->map(function ($item) {
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

                if ($this->isTembakau($product)) {
                    $itemSubtotal = $product->sell_price * ($item['qty'] / 100);
                } else {
                    $itemSubtotal = $product->sell_price * $item['qty'];
                }

                return [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'subtotal' => $itemSubtotal,
                    'stock_reduction' => $stockReduction,
                ];
            })->values()->all();

            $summary = $this->calculateCartSummary($cart, $items);
            $storeSettings = $this->getStoreSettings();
            $taxPercentage = $storeSettings->tax_percentage ?? 0;
            $taxAmount = $summary['taxAmount'];
            $totalBeforeRound = $summary['subtotal'] + $taxAmount;
            $rounding = (int) ($storeSettings->rounding ?? 0);
            $roundingAmount = 0;
            $total = $summary['grandTotal'];

            if ($rounding > 0) {
                $rounded = (float) (round($totalBeforeRound / $rounding) * $rounding);
                $roundingAmount = round($rounded - $totalBeforeRound, 2);
                $total = round($totalBeforeRound + $roundingAmount, 2);
            }

            $paidAmount = null;
            $changeAmount = 0;

            if ($isQris) {
                $paidAmount = $total;
                $changeAmount = 0;
            } else {
                $paidAmount = $data['paid_amount'] ?? null;

                if ($paidAmount === null || $paidAmount === '') {
                    abort(422, 'Nominal pembayaran kurang.');
                }

                if ((float) $paidAmount < $total) {
                    abort(422, 'Nominal pembayaran kurang.');
                }

                $changeAmount = (float) $paidAmount - $total;
            }

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
                'subtotal' => $summary['subtotal'],
                'discount' => 0,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total_before_round' => $totalBeforeRound,
                'rounding' => $rounding,
                'rounding_amount' => $roundingAmount,
                'total' => $total,
                'payment_method_id' => $data['payment_method_id'],
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'status' => 'completed',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_transaction',
                'auditable_type' => Transaction::class,
                'auditable_id' => $transaction->id,
                'description' => 'Transaction created ' . $transaction->invoice_no,
                'ip_address' => request()->ip(),
            ]);

            foreach ($items as $item) {
                $product = $item['product'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->sell_price,
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'buy_price' => $product->buy_price ?? 0,
                    'sell_price' => $product->sell_price ?? 0,
                    'product_name' => $product->name ?? 'Produk telah dihapus',
                    'product_unit' => $product->unit ?? $product->stock_unit ?? 'pcs',
                    'product_category' => $product->category?->name ?? '-',
                    'product_barcode' => $product->barcode ?? '-',
                ]);

                $product->decrement(
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
            ->route('pos.receipt.page', ['transaction' => $transaction])
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Product $product)
    {
        if (! $product->is_active || $product->stock <= 0) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $cart = $this->getCart();
        $requestedQty = request()->input('qty');

        $qty = $requestedQty !== null ? $requestedQty + 0 : null;
        $unit = $product->stockUnit();

        if (isset($cart[$product->id])) {
            $newQty = $qty === null ? $cart[$product->id]['qty'] + 1 : $cart[$product->id]['qty'] + $qty;
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

        $this->saveCart($cart);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update the quantity of an existing cart item.
     */
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

        $cart = $this->getCart();

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ada di keranjang.');
        }

        $requiredStock = $this->resolveStockReduction($product, $data['qty']);

        if ($requiredStock > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        $cart[$product->id]['qty'] = $data['qty'];
        $this->saveCart($cart);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Product $product)
    {
        $cart = $this->getCart();

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ada di keranjang.');
        }

        unset($cart[$product->id]);
        $this->saveCart($cart);

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Clear all cart items.
     */
    public function clearCart()
    {
        $this->saveCart([]);

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    /**
     * Get the current cart from session and normalize it.
     */
    private function getCart(): array
    {
        $cart = session('cart', []);

        $normalizedCart = $this->normalizeCart($cart);
        $this->saveCart($normalizedCart);

        return $normalizedCart;
    }

    /**
     * Persist the cart back to session.
     */
    private function saveCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    /**
     * Normalize cart items and enrich them with product details.
     */
    private function normalizeCart(array $cart): array
    {
        $productIds = collect($cart)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $products = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function ($item) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return $item;
                }

                $item['unit'] = $item['unit'] ?? $product->unit;
                $item['is_tembakau'] = $this->isTembakau($product);

                return $item;
            })
            ->toArray();
    }

    /**
     * Calculate price summary for the cart.
     */
    private function calculateCartSummary(array $cart, ?array $items = null): array
    {
        $subtotal = $items !== null
            ? (float) collect($items)->sum('subtotal')
            : (float) collect($cart)->sum(function ($item) {
                $price = (float) ($item['price'] ?? 0);
                $qty = (float) ($item['qty'] ?? 0);

                if ($item['is_tembakau'] ?? false) {
                    return $price * ($qty / 100);
                }

                return $price * $qty;
            });

        $storeSettings = $this->getStoreSettings();
        $discount = 0;
        $taxPercentage = (float) ($storeSettings->tax_percentage ?? 0);
        $taxAmount = $taxPercentage > 0
            ? round($subtotal * ($taxPercentage / 100), 2)
            : 0;

        $rounding = (int) ($storeSettings->rounding ?? 0);
        $grandTotal = $subtotal + $taxAmount - $discount;

        if ($rounding > 0) {
            $grandTotal = round($grandTotal / $rounding) * $rounding;
        }

        return [
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'discount' => $discount,
            'grandTotal' => $grandTotal,
        ];
    }

    /**
     * Get active products for the POS catalog.
     */
    private function getActiveProducts()
    {
        return Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active payment methods.
     */
    private function getPaymentMethods()
    {
        return PaymentMethod::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get store settings.
     */
    private function getStoreSettings(): StoreSetting
    {
        $storeSettings = StoreSetting::first();

        if (! $storeSettings) {
            $storeSettings = new StoreSetting();
            $storeSettings->tax_percentage = 0;
            $storeSettings->rounding = 0;
            $storeSettings->transaction_number_format = null;
        }

        return $storeSettings;
    }

    protected function resolveStockReduction(Product $product, float $qty): int
    {
        return (int) round($qty);
    }

    protected function isValidCartQuantity(Product $product, $qty): bool
    {
        // For tembakau (measured in grams), allow any numeric quantity
        if (($product->stock_unit ?? '') === 'gram' || ($product->selling_unit ?? '') === 'gram') {
            return is_numeric($qty) && $qty > 0;
        }

        // For regular items, require integer quantity
        return is_int($qty + 0) || floor($qty) == $qty;
    }

    protected function isTembakau(Product $product): bool
    {
        return $product->selling_unit === 'gram' || $product->stock_unit === 'gram';
    }

    /**
     * Scan barcode and return product details
     * 
     * Production-ready endpoint with comprehensive:
     * - Input sanitization & validation
     * - Error handling for all scenarios
     * - Audit logging for security
     * - Stock verification
     * - Product status checking
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scanBarcode(Request $request)
    {
        try {
            // ============================================================
            // 1. INPUT SANITIZATION
            // ============================================================
            
            $rawBarcode = $request->query('barcode', '');
            
            // Remove all leading/trailing whitespace
            $barcode = trim($rawBarcode);
            
            // Remove internal whitespace
            $barcode = preg_replace('/\s+/', '', $barcode);
            
            // Convert to uppercase for consistency
            $barcode = strtoupper($barcode);
            
            // Remove potentially dangerous characters (keep only alphanumeric, dash, underscore)
            $barcode = preg_replace('/[^A-Z0-9\-_]/', '', $barcode);
            
            $barcodeLength = strlen($barcode);

            // ============================================================
            // 2. INITIAL VALIDATION
            // ============================================================
            
            Log::channel('barcode')->info('SCAN_START', [
                'raw_input' => $rawBarcode,
                'sanitized' => $barcode,
                'length' => $barcodeLength,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'timestamp' => now()->toIso8601String(),
            ]);

            // Empty check
            if (empty($barcode)) {
                Log::channel('barcode')->warning('SCAN_EMPTY', [
                    'user_id' => auth()->id(),
                    'reason' => 'Empty after sanitization',
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Barcode kosong atau tidak valid.',
                ], 400);
            }

            // Length validation (EAN-13: 13 chars, CODE-128/39: typically 5-50)
            if ($barcodeLength < 5 || $barcodeLength > 50) {
                Log::channel('barcode')->warning('SCAN_INVALID_LENGTH', [
                    'barcode' => $barcode,
                    'length' => $barcodeLength,
                    'user_id' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Format barcode tidak valid (panjang harus 5-50 karakter).',
                ], 400);
            }

            // ============================================================
            // 3. DATABASE SEARCH
            // ============================================================
            
            // Search with case-insensitive comparison
            $product = Product::whereRaw(
                'TRIM(LOWER(barcode)) = ?',
                [strtolower($barcode)]
            )
            ->where('is_active', true)
            ->first();

            Log::channel('barcode')->info('BARCODE_SEARCH_RESULT', [
                'barcode' => $barcode,
                'found' => $product !== null,
                'product_id' => $product?->id,
                'product_name' => $product?->name,
                'user_id' => auth()->id(),
            ]);

            // Product not found
            if (!$product) {
                Log::channel('barcode')->notice('PRODUCT_NOT_FOUND', [
                    'barcode' => $barcode,
                    'user_id' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Produk dengan barcode ini tidak ditemukan.',
                ], 404);
            }

            // ============================================================
            // 4. PRODUCT STATUS VALIDATION
            // ============================================================
            
            // Check if product is active (should be true from query, but double-check)
            if (!$product->is_active) {
                Log::channel('barcode')->warning('PRODUCT_INACTIVE', [
                    'product_id' => $product->id,
                    'barcode' => $barcode,
                    'user_id' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak aktif lagi.',
                ], 422);
            }

            // Check stock availability
            if ($product->stock <= 0) {
                Log::channel('barcode')->notice('PRODUCT_OUT_OF_STOCK', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'barcode' => $barcode,
                    'stock' => $product->stock,
                    'user_id' => auth()->id(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sedang kosong (stok: ' . $product->stock . ').',
                ], 422);
            }

            // ============================================================
            // 5. SUCCESS RESPONSE
            // ============================================================
            
            $responseData = [
                'success' => true,
                'product' => [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->sell_price,
                    'barcode' => $product->barcode,
                    'stock' => (int) $product->stock,
                    'selling_unit' => $product->selling_unit,
                    'is_tembakau' => $this->isTembakau($product),
                ],
            ];

            Log::channel('barcode')->info('SCAN_SUCCESS', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'barcode' => $barcode,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'timestamp' => now()->toIso8601String(),
            ]);

            // Create audit log for successful scan
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'scan_barcode',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'description' => 'Barcode scanned: ' . $product->name,
                'ip_address' => $request->ip(),
            ]);

            return response()->json($responseData);

        } catch (\Exception $e) {
            // Catch-all error handler
            Log::channel('barcode')->error('SCAN_ERROR_EXCEPTION', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat memproses barcode.',
            ], 500);
        }
    }

    public function transactions(Request $request)
    {
        $payload = $this->buildTransactionsPayload($request);

        if ($request->expectsJson()) {
            return response()->json([
                'summary' => $payload['summary'],
                'categories' => $payload['categories'],
                'transactions_html' => view('pos.partials.transaction-list', ['transactions' => $payload['transactions']])->render(),
            ]);
        }

        return view('pos.transactions', [
            'transactions' => $payload['transactions'],
            'summary' => $payload['summary'],
            'categories' => $payload['categories'],
        ]);
    }

    public function transactionsData(Request $request)
    {
        $payload = $this->buildTransactionsPayload($request);

        return response()->json([
            'summary' => $payload['summary'],
            'categories' => $payload['categories'],
            'transactions_html' => view('pos.partials.transaction-list', ['transactions' => $payload['transactions']])->render(),
        ]);
    }

    private function buildTransactionsPayload(Request $request): array
    {
        $query = $this->buildTransactionsQuery($request);
        $transactions = (clone $query)->latest()->get();

        $salesQuery = (clone $query)->where('status', 'completed');
        $salesCount = (clone $salesQuery)->count();
        $totalSales = (float) (clone $salesQuery)->sum('total');

        $transactionIds = (clone $salesQuery)->pluck('id');
        $totalItemsSold = 0;

        if ($transactionIds->isNotEmpty()) {
            $totalItemsSold = (int) TransactionItem::whereIn('transaction_id', $transactionIds)->sum('qty');
        }

        $summary = [
            'total_transactions' => (int) $query->count(),
            'total_sales' => round($totalSales, 2),
            'total_items_sold' => $totalItemsSold,
            'average_transaction_value' => $salesCount > 0 ? round($totalSales / $salesCount, 2) : 0,
        ];

        $categories = [];

        if ($transactionIds->isNotEmpty()) {
            $categoryRows = DB::table('transaction_items')
                ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->join('products', 'products.id', '=', 'transaction_items.product_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->whereIn('transaction_items.transaction_id', $transactionIds)
                ->select(
                    DB::raw('COALESCE(categories.name, "Tanpa Kategori") as category_name'),
                    DB::raw('SUM(transaction_items.qty) as total_items_sold'),
                    DB::raw('SUM(transaction_items.subtotal) as total_sales')
                )
                ->groupBy(DB::raw('COALESCE(categories.name, "Tanpa Kategori")'))
                ->orderByDesc('total_items_sold')
                ->get();

            $categories = $categoryRows->map(function ($row) {
                return [
                    'category_name' => (string) $row->category_name,
                    'total_items_sold' => (int) $row->total_items_sold,
                    'total_sales' => round((float) $row->total_sales, 2),
                ];
            })->values()->all();
        }

        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'categories' => $categories,
        ];
    }

    private function buildTransactionsQuery(?Request $request = null)
    {
        $query = Transaction::query()
            ->where('user_id', auth()->id())
            ->with([
                'items' => function ($query) {
                    $query->with(['product' => function ($query) {
                        $query->with('category');
                    }]);
                },
                'paymentMethod',
            ]);

        $filter = $request?->input('filter', 'all') ?? 'all';
        $search = trim((string) ($request?->input('search', '') ?? ''));

        if ($filter === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($filter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'month') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($search !== '') {
            $query->where('invoice_no', 'like', "%{$search}%");
        }

        return $query;
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