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
use Illuminate\Support\Carbon;
use App\Models\AuditLog;
use App\Models\SavedOrder;
use App\Models\SavedOrderItem;
use App\Http\Controllers\Traits\CartHelpers;


class PosController extends Controller
{
    use CartHelpers;
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
        $savedOrders = SavedOrder::where('user_id', auth()->id())
            ->withCount('items')
            ->latest()
            ->get();

        return view('pos.index', [
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'cart' => $cart,
            'subtotal' => $summary['subtotal'],
            'taxAmount' => $summary['taxAmount'],
            'discount' => $summary['discount'],
            'grandTotal' => $summary['grandTotal'],
            'cartCount' => $cartCount,
            'savedOrders' => $savedOrders,
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

                $itemPrice = $item['price'] ?? $product->sell_price;
                $itemPurchaseType = $item['purchase_type'] ?? null;
                $itemSubtotal = $item['subtotal'] ?? ($this->isTembakau($product)
                    ? $itemPrice * ($item['qty'] / 100)
                    : $itemPrice * $item['qty']);

                return [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'price' => $itemPrice,
                    'purchase_type' => $itemPurchaseType,
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
                    'price' => $item['price'],
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'buy_price' => $product->buy_price ?? 0,
                    'sell_price' => $product->sell_price ?? 0,
                    'purchase_type' => $item['purchase_type'] ?? null,
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

        // If this checkout was for a loaded saved order, remove it
        $savedOrderId = session('saved_order_id');
        if ($savedOrderId) {
            $saved = SavedOrder::where('id', $savedOrderId)->where('user_id', auth()->id())->first();
            if ($saved) {
                $saved->items()->delete();
                $saved->delete();
            }
            session()->forget('saved_order_id');
        }

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
            return response()->json(['success' => false, 'message' => 'Produk tidak tersedia.'], 404);
        }

        $cart = $this->getCart();
        $requestedQty = request()->input('qty');
        $purchaseType = request()->input('purchase_type');
        $inputMethod = request()->input('input_method');
        $requestedPrice = request()->input('price');

        $qty = $requestedQty !== null ? $requestedQty + 0 : null;
        $unit = $product->sellingUnit();
        $saleType = $product->saleType();
        $selectedPurchaseType = $purchaseType ?? ($saleType === 'gram' ? 'gram' : 'pcs');

        $price = $requestedPrice !== null
            ? (float) $requestedPrice
            : (isset($cart[$product->id]['price']) ? (float) $cart[$product->id]['price'] : (float) $product->sell_price);

        if ($purchaseType !== null) {
            $price = $this->resolveCartPrice($product, $purchaseType);
        }

        if (isset($cart[$product->id])) {
            $newQty = $qty === null ? $cart[$product->id]['qty'] + 1 : $cart[$product->id]['qty'] + $qty;
            $currentPurchaseType = $purchaseType ?? ($cart[$product->id]['purchase_type'] ?? $selectedPurchaseType);

            if ($currentPurchaseType === 'grosir') {
                $minQty = $product->wholesale_min_qty ?? null;
                if ($minQty !== null && $newQty < $minQty) {
                    return response()->json(['success' => false, 'message' => "Minimal pembelian grosir adalah {$minQty} pcs."], 422);
                }
            }

            $requiredStock = $this->resolveStockReduction($product, $newQty);

            if ($requiredStock > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Jumlah melebihi stok tersedia.'], 422);
            }

            $cart[$product->id]['qty'] = $newQty;
            $cart[$product->id]['unit'] = $unit;
            $cart[$product->id]['sale_type'] = $saleType;
            $cart[$product->id]['purchase_type'] = $currentPurchaseType;
            $cart[$product->id]['input_method'] = $inputMethod ?? ($cart[$product->id]['input_method'] ?? (str_contains($saleType, 'gram') ? 'berat' : null));
            $cart[$product->id]['price'] = $price;
            $cart[$product->id]['subtotal'] = $this->calculateCartItemSubtotal($product, $price, $newQty);
            $cart[$product->id]['wholesale_price'] = (float) ($product->wholesale_price ?? 0);
            $cart[$product->id]['wholesale_min_qty'] = (int) ($product->wholesale_min_qty ?? 0);
            $cart[$product->id]['is_tembakau'] = $this->isTembakau($product);
        } else {
            $initialQty = $qty === null ? 1 : $qty;
            $selectedPurchaseType = $purchaseType ?? ($saleType === 'gram' ? 'gram' : 'pcs');

            if ($selectedPurchaseType === 'grosir') {
                $minQty = $product->wholesale_min_qty ?? null;
                if ($minQty !== null && $initialQty < $minQty) {
                    return response()->json(['success' => false, 'message' => "Minimal pembelian grosir adalah {$minQty} pcs."], 422);
                }
            }

            $requiredStock = $this->resolveStockReduction($product, $initialQty);

            if ($requiredStock > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Jumlah melebihi stok tersedia.'], 422);
            }

            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'qty' => $initialQty,
                'unit' => $unit,
                'sale_type' => $saleType,
                'purchase_type' => $selectedPurchaseType,
                'input_method' => $inputMethod ?? (str_contains($saleType, 'gram') ? 'berat' : null),
                'subtotal' => $this->calculateCartItemSubtotal($product, $price, $initialQty),
                'wholesale_price' => (float) ($product->wholesale_price ?? 0),
                'wholesale_min_qty' => (int) ($product->wholesale_min_qty ?? 0),
                'is_tembakau' => $this->isTembakau($product),
            ];
        }

        $this->saveCart($cart);

        $summary = $this->calculateCartSummary($cart);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'cart_html' => view('pos.partials.cart-items', ['cart' => $cart])->render(),
            'summary' => [
                'items' => count($cart),
                'gram' => collect($cart)->where('is_tembakau', true)->sum('qty'),
                'subtotal' => $summary['subtotal'] ?? 0,
                'grand_total' => $summary['grandTotal'] ?? $summary['grand_total'] ?? 0,
            ],
            'cart_count' => count($cart),
        ]);
    }

    private function resolveCartPrice(Product $product, ?string $purchaseType): float
    {
        $defaultPrice = (float) ($product->sell_price ?? 0);

        if ($purchaseType === 'grosir' && (float) ($product->wholesale_price ?? 0) > 0) {
            return (float) $product->wholesale_price;
        }

        return $defaultPrice;
    }

    /**
     * Update the quantity of an existing cart item.
     */
    public function updateCart(Request $request, Product $product)
    {
        $rules = [];

        // accept optional purchase_type and input_method
        $rules['purchase_type'] = ['nullable', 'string'];
        $rules['input_method'] = ['nullable', 'string'];

        if ($product->saleType() && str_contains($product->saleType(), 'gram')) {
            $rules['qty'] = ['required', 'numeric', 'gt:0'];
        } else {
            $rules['qty'] = ['required', 'integer', 'min:1'];
        }

        $data = $request->validate($rules);

        $cart = $this->getCart();

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ada di keranjang.');
        }

        $requiredStock = $this->resolveStockReduction($product, $data['qty']);

        if ($requiredStock > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok tersedia.');
        }

        // enforce wholesale minima when applicable
        $purchaseType = $data['purchase_type'] ?? ($cart[$product->id]['purchase_type'] ?? null);
        if ($purchaseType === 'grosir') {
            $min = $product->wholesale_min_qty ?? null;
            if ($min !== null && $data['qty'] < $min) {
                $unitLabel = str_contains($product->saleType(), 'gram') ? 'gram' : 'pcs';
                return back()->with('error', "Minimal pembelian grosir adalah {$min} {$unitLabel}.");
            }
        }

        $cart[$product->id]['qty'] = $data['qty'];
        if (isset($data['purchase_type'])) {
            $cart[$product->id]['purchase_type'] = $data['purchase_type'];
            $cart[$product->id]['price'] = $this->resolveCartPrice($product, $data['purchase_type']);
        }
        if (isset($data['input_method'])) $cart[$product->id]['input_method'] = $data['input_method'];
        if (isset($data['price'])) $cart[$product->id]['price'] = (float) $data['price'];
        $cart[$product->id]['subtotal'] = $this->calculateCartItemSubtotal($product, $cart[$product->id]['price'], $cart[$product->id]['qty']);

        $this->saveCart($cart);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    /**
     * Remove a product from the cart.
     */
    private function calculateCartItemSubtotal(Product $product, float $price, float $qty): float
    {
        if ($this->isTembakau($product)) {
            return $price * ($qty / 100);
        }

        return $price * $qty;
    }

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
    // Cart helper methods moved to CartHelpers trait.

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
                    'category_name' => $product->category?->name ?? '-',
                    'price' => (float) $product->sell_price,
                    'barcode' => $product->barcode,
                    'stock' => (int) $product->stock,
                    'selling_unit' => $product->selling_unit,
                    'sale_type' => $product->saleType(),
                    'wholesale_price' => (float) ($product->wholesale_price ?? 0),
                    'wholesale_min_qty' => (int) ($product->wholesale_min_qty ?? 0),
                    'wholesale_price_per_gram' => $product->saleType() && str_contains($product->saleType(), 'gram')
                        ? round((float) ($product->wholesale_price ?? 0) / 100, 2)
                        : null,
                    'is_tembakau' => $this->isTembakau($product),
                    // price unit metadata for client-side conversions
                    'price_unit' => $product->priceUnit(),
                    'price_per_gram' => (float) $product->pricePerGram(),
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

        $cashSalesQuery = (clone $salesQuery)->whereHas('paymentMethod', function ($query) {
            $query->where(function ($query) {
                $query->where('name', 'like', '%cash%')
                    ->orWhere('name', 'like', '%tunai%')
                    ->orWhere('code', 'like', '%cash%')
                    ->orWhere('code', 'like', '%tunai%');
            });
        });

        $nonCashSalesQuery = (clone $salesQuery)->where(function ($query) {
            $query->whereHas('paymentMethod', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'not like', '%cash%')
                        ->where('name', 'not like', '%tunai%')
                        ->where('code', 'not like', '%cash%')
                        ->where('code', 'not like', '%tunai%');
                });
            });
        });

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
            'total_cash_sales' => round((float) $cashSalesQuery->sum('total'), 2),
            'cash_transaction_count' => (int) $cashSalesQuery->count(),
            'total_non_cash_sales' => round((float) $nonCashSalesQuery->sum('total'), 2),
            'non_cash_transaction_count' => (int) $nonCashSalesQuery->count(),
            'grand_total' => round($totalSales, 2),
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
                'user',
                'items' => function ($query) {
                    $query->with(['product' => function ($query) {
                        $query->with('category');
                    }]);
                },
                'paymentMethod',
            ]);

        $filter = $request?->input('filter', 'all') ?? 'all';
        $paymentType = $request?->input('payment_type', 'all') ?? 'all';
        $search = trim((string) ($request?->input('search', '') ?? ''));
        $fromDate = trim((string) ($request?->input('from_date', '') ?? ''));
        $toDate = trim((string) ($request?->input('to_date', '') ?? ''));

        if ($filter === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($filter === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'month') {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($fromDate !== '') {
            $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->toDateString());
        }

        if ($toDate !== '') {
            $query->whereDate('created_at', '<=', Carbon::parse($toDate)->toDateString());
        }

        if ($paymentType === 'cash') {
            $query->whereHas('paymentMethod', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%cash%')
                        ->orWhere('name', 'like', '%tunai%')
                        ->orWhere('code', 'like', '%cash%')
                        ->orWhere('code', 'like', '%tunai%');
                });
            });
        } elseif ($paymentType === 'non_cash') {
            $query->whereHas('paymentMethod', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'not like', '%cash%')
                        ->where('name', 'not like', '%tunai%')
                        ->where('code', 'not like', '%cash%')
                        ->where('code', 'not like', '%tunai%');
                });
            });
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
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