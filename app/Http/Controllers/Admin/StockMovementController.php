<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with([
            'product',
            'user',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock.movements', compact('movements'));
    }
}