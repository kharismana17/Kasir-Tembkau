<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'stock_adjustment',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'description' => 'Stock adjusted by ' . $data['change'] . '. Note: ' . ($data['note'] ?? ''),
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()
            ->route('admin.stock.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }

    public function opnameIndex()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'stock_min')
            ->count();

        return view('admin.stock.opname', compact('products', 'lowStockCount'));
    }

    public function opnameStore(Request $request)
    {
        $data = $request->validate([
            'stock_physical' => ['required', 'array'],
            'stock_physical.*' => ['required', 'integer', 'min:0'],
            'opname_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $note = $data['opname_note'] ?? 'Stok opname';

            foreach ($data['stock_physical'] as $productId => $physicalStock) {
                $product = Product::find($productId);

                if (! $product || ! $product->is_active) {
                    continue;
                }

                $systemStock = $product->stock;
                $physicalStock = (int) $physicalStock;
                $difference = $physicalStock - $systemStock;

                if ($difference === 0) {
                    continue;
                }

                $product->stock = $physicalStock;
                $product->save();

                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $difference,
                    'type' => 'stock_adjustment',
                    'reference_type' => 'stock_opname',
                    'reference_id' => null,
                    'user_id' => Auth::id(),
                    'note' => $note,
                ]);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'stock_opname',
                    'auditable_type' => Product::class,
                    'auditable_id' => $product->id,
                    'description' => 'Stok opname: sistem ' . $systemStock . ', fisik ' . $physicalStock . '. Note: ' . $note,
                    'ip_address' => $request->ip(),
                ]);
            }
        });

        return redirect()
            ->route('admin.stock.opname.index')
            ->with('success', 'Stok opname berhasil disimpan.');
    }

    public function adjustCreate(Product $product)
    {
        return view('admin.stock.adjust', compact('product'));
    }

    public function adjustStore(Request $request, Product $product)
    {
        $data = $request->validate([
            'action' => ['required', 'in:add,reduce'],
            'amount' => ['required', 'integer', 'min:1'],
            'note' => ['required', 'string', 'max:500'],
        ]);

        $amount = (int) $data['amount'];
        $currentStock = $product->stock;
        $change = $data['action'] === 'add' ? $amount : -$amount;
        $newStock = $currentStock + $change;

        if ($newStock < 0) {
            return back()
                ->withErrors(['amount' => 'Stok tidak boleh negatif.'])
                ->withInput();
        }

        DB::transaction(function () use ($data, $product, $change, $newStock, $request) {
            $product->stock = $newStock;
            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'change' => $change,
                'type' => 'stock_adjustment',
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'user_id' => Auth::id(),
                'note' => $data['note'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'stock_adjustment',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'description' => 'Penyesuaian stok ' . ($change > 0 ? 'tambah' : 'kurang') . ' ' . abs($change) . '. Note: ' . $data['note'],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()
            ->route('admin.stock.index')
            ->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
}
