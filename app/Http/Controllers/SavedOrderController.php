<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SavedOrder;
use App\Models\SavedOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\CartHelpers;

class SavedOrderController extends Controller
{
    use CartHelpers;
    /**
     * List saved orders for current user
     */
    public function index()
    {
        $orders = SavedOrder::where('user_id', auth()->id())->latest()->get();

        return view('pos.saved_orders.index', compact('orders'));
    }

    /**
     * Save current session cart into saved_orders
     */
    public function save(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Keranjang masih kosong.');
        }

        DB::transaction(function () use ($cart, &$savedOrder) {
            $summary = $this->calculateCartSummary($cart);

            $savedOrder = SavedOrder::create([
                'user_id' => auth()->id(),
                'subtotal' => $summary['subtotal'],
                'tax_amount' => $summary['taxAmount'],
                'total' => $summary['grandTotal'],
                'total_items' => array_sum(array_map(fn($i) => $i['qty'] ?? 0, $cart)),
            ]);

            foreach ($cart as $item) {
                SavedOrderItem::create([
                    'saved_order_id' => $savedOrder->id,
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['name'] ?? '',
                    'price' => $item['price'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'unit' => $item['unit'] ?? null,
                    'sale_type' => $item['sale_type'] ?? null,
                    'purchase_type' => $item['purchase_type'] ?? null,
                    'input_method' => $item['input_method'] ?? null,
                    'is_tembakau' => $item['is_tembakau'] ?? false,
                    'subtotal' => $item['is_tembakau'] ?? false
                        ? ($item['price'] * ($item['qty'] / 100))
                        : ($item['price'] * ($item['qty'] ?? 0)),
                ]);
            }
        });

        // Clear session cart and any saved_order_id
        session()->forget('cart');
        session()->forget('saved_order_id');

        return redirect()->route('pos.index')->with('success', 'Saved order berhasil disimpan.');
    }

    /**
     * Load a saved order into session cart
     */
    public function load(SavedOrder $savedOrder)
    {
        if ($savedOrder->user_id !== auth()->id()) {
            abort(403);
        }

        // rebuild cart structure
        $cart = [];

        foreach ($savedOrder->items as $item) {
            $cart[$item->product_id ?? uniqid('x')] = [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'qty' => (float) $item->qty,
                'unit' => $item->unit,
                'sale_type' => $item->sale_type,
                'purchase_type' => $item->purchase_type,
                'input_method' => $item->input_method,
                'is_tembakau' => (bool) $item->is_tembakau,
                'saved_order_id' => $savedOrder->id,
            ];
        }

        session()->put('cart', $cart);
        session()->put('saved_order_id', $savedOrder->id);

        return redirect()->route('pos.index');
    }

    /**
     * Delete saved order
     */
    public function delete(SavedOrder $savedOrder)
    {
        if ($savedOrder->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($savedOrder) {
            $savedOrder->items()->delete();
            $savedOrder->delete();
        });

        if (session('saved_order_id') === $savedOrder->id) {
            session()->forget('saved_order_id');
        }

        return redirect()->route('pos.index')->with('success', 'Saved order dihapus.');
    }
}
