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

            if ($data['paid_amount'] < $subtotal) {
                abort(422, 'Nominal pembayaran kurang.');
            }

            $transaction = Transaction::create([
                'invoice_no' => 'TRX-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => 0,
                'total' => $subtotal,
                'payment_method_id' => $data['payment_method_id'],
                'paid_amount' => $data['paid_amount'],
                'change_amount' => $data['paid_amount'] - $subtotal,
                'status' => 'completed',
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

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['qty'] + 1;
            $requiredStock = $this->resolveStockReduction($product, $newQty);

            if ($requiredStock > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok tersedia.');
            }

            $cart[$product->id]['qty'] = $newQty;
            $cart[$product->id]['unit'] = $product->unit;
            $cart[$product->id]['is_tembakau'] = $this->isTembakau($product);
        } else {
            $requiredStock = $this->resolveStockReduction($product, 1);

            if ($requiredStock > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok tersedia.');
            }

            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sell_price,
                'qty' => 1,
                'unit' => $product->unit,
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
        // If selling unit is ons (tembakau), qty is in ons, need to convert to gram (stock unit)
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
}