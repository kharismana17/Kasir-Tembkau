<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedOrder;
use App\Models\SavedOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavedOrderController extends Controller
{
    public function save(Request $request)
    {
        $user = auth()->user();

        // Generate a unique order number
        $orderNumber = 'SO-' . Str::upper(Str::random(8));

        // Create a new saved order
        $savedOrder = SavedOrder::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'customer_name' => $request->input('customer_name'),
            'total' => 0, // Will be calculated later
            'total_items' => 0, // Will be calculated later
        ]);

        $cartItems = session('cart', []);
        $total = 0;
        $totalItems = 0;

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $subtotal = $item['qty'] * $product->price;
                SavedOrderItem::create([
                    'saved_order_id' => $savedOrder->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
                $totalItems += $item['qty'];
            }
        }

        // Update the total and total_items in the saved order
        $savedOrder->update([
            'total' => $total,
            'total_items' => $totalItems,
        ]);

        // Clear the cart after saving
        session()->forget('cart');

        return back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
        $user = auth()->user();
        $savedOrders = SavedOrder::where('user_id', $user->id)->get();

        return view('saved_orders.index', compact('savedOrders'));
    }

    public function load($id)
    {
        $savedOrder = SavedOrder::with('items.product')->findOrFail($id);

        // Clear the current cart
        session()->forget('cart');

        // Load saved order items into the cart
        $cartItems = [];
        foreach ($savedOrder->items as $item) {
            $cartItems[] = [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
            ];
        }

        session(['cart' => $cartItems]);

        return redirect()->route('pos.index')->with('success', 'Saved order loaded into the cart.');
    }

    public function delete($id)
    {
        $savedOrder = SavedOrder::findOrFail($id);
        $savedOrder->delete();

        return back()->with('success', 'Saved order deleted successfully.');
    }
}