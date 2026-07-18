<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.stock.index', compact('products'));
    }

    public function create(Product $product)
    {
        return view('admin.stock.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'change' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data, $product) {
            $product->increment('stock', $data['change']);

            StockMovement::create([
                'product_id' => $product->id,
                'change' => $data['change'],
                'type' => 'stock_in',
                'reference_type' => 'manual',
                'reference_id' => null,
                'user_id' => Auth::id(),
                'note' => $data['note'] ?? 'Stok masuk manual',
            ]);
        });

        return redirect()
            ->route('admin.stock.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }
}