<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use App\Models\StoreSetting;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        }

        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view(
            'admin.products.index',
            compact('products', 'categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $storeSettings = StoreSetting::first();

        return view(
            'admin.products.create',
            compact('categories', 'storeSettings')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'buy_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sell_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'stock_min' => [
                'required',
                'integer',
                'min:0',
            ],

            'sale_type' => [
                'nullable',
                'in:pcs,gram,pack,pcs_grosir,gram_grosir',
            ],

            'wholesale_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'wholesale_min_qty' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UNIT DAN TIPE JUAL
        |--------------------------------------------------------------------------
        */

        $data['sale_type'] = $data['sale_type'] ?? Product::resolveSaleTypeByCategory($data['category_id']);
        $units = Product::resolveUnitsBySaleType($data['sale_type']);

        $data['stock_unit'] = $units['stock_unit'];
        $data['selling_unit'] = $units['selling_unit'];
        $data['unit'] = $units['selling_unit'];


        /*
        |--------------------------------------------------------------------------
        | SKU & BARCODE OTOMATIS
        |--------------------------------------------------------------------------
        */

        $data['sku'] = Product::generateUniqueSku(
            $data['name']
        );

        $data['barcode'] = Product::generateUniqueBarcode();


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $data['is_active'] = true;


        /*
        |--------------------------------------------------------------------------
        | CREATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product = Product::create($data);


        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG
        |--------------------------------------------------------------------------
        */

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_product',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
            'description' => 'Product created: ' . $product->name,
            'ip_address' => request()->ip(),
        ]);


        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Produk berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view(
            'admin.products.edit',
            compact('product', 'categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Product $product
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'buy_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sell_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'stock_min' => [
                'required',
                'integer',
                'min:0',
            ],

            'sale_type' => [
                'nullable',
                'in:pcs,gram,pack,pcs_grosir,gram_grosir',
            ],

            'wholesale_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'wholesale_min_qty' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UNIT DAN TIPE JUAL
        |--------------------------------------------------------------------------
        */

        $data['sale_type'] = $data['sale_type'] ?? Product::resolveSaleTypeByCategory($data['category_id']);
        $units = Product::resolveUnitsBySaleType($data['sale_type']);

        $data['stock_unit'] = $units['stock_unit'];
        $data['selling_unit'] = $units['selling_unit'];
        $data['unit'] = $units['selling_unit'];


        /*
        |--------------------------------------------------------------------------
        | SIMPAN HARGA LAMA
        |--------------------------------------------------------------------------
        */

        $originalPrice = $product->sell_price;


        /*
        |--------------------------------------------------------------------------
        | UPDATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product->update($data);


        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG UPDATE
        |--------------------------------------------------------------------------
        */

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_product',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
            'description' => 'Product updated: ' . $product->name,
            'ip_address' => request()->ip(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG HARGA
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['sell_price']) &&
            $data['sell_price'] != $originalPrice
        ) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update_price',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'description' =>
                    'Price changed from ' .
                    $originalPrice .
                    ' to ' .
                    $data['sell_price'],
                'ip_address' => request()->ip(),
            ]);
        }


        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Produk berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE / TOGGLE STATUS
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        // Cek apakah produk pernah dipakai transaksi
        if ($product->transactionItems()->exists()) {

            $product->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'warning',
                    'Produk sudah pernah digunakan pada transaksi sehingga tidak dapat dihapus. Produk dinonaktifkan.'
                );
        }

        // Jika belum pernah dipakai transaksi
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | BARCODE IMAGE
    |--------------------------------------------------------------------------
    */

    public function barcode(Product $product)
    {
        // Buat barcode otomatis jika masih kosong
        if (empty($product->barcode)) {
            $product->barcode = 'BRG' . str_pad($product->id, 8, '0', STR_PAD_LEFT);
            $product->save();
        }

        $generator = new BarcodeGeneratorSVG();

        $barcode = $generator->getBarcode(
            (string) $product->barcode,
            $generator::TYPE_CODE_128
        );

        return response($barcode)
            ->header('Content-Type', 'image/svg+xml');
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT 1 PRODUK
    |--------------------------------------------------------------------------
    |
    | 3 kolom x 8 baris = 24 barcode per lembar A4
    |--------------------------------------------------------------------------
    */

    public function printBarcode(Product $product)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();

        if (empty($product->barcode)) {
            $product->barcode = 'BRG' . str_pad($product->id, 8, '0', STR_PAD_LEFT);
            $product->save();
        }

        $barcode = $generator->getBarcode(
            (string) $product->barcode,
            $generator::TYPE_CODE_128
        );

        return view('admin.products.print-barcode', compact('product', 'barcode'));
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT SEMUA BARCODE
    |--------------------------------------------------------------------------
    */

    public function printAllBarcodes()
    {
        $generator = new BarcodeGeneratorSVG();

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($product) use ($generator) {

                // Jika barcode kosong, buat barcode otomatis
                if (empty($product->barcode)) {
                    $product->barcode = 'BRG' . str_pad($product->id, 8, '0', STR_PAD_LEFT);
                    $product->save();
                }

                return [
                    'product' => $product,
                    'barcode' => $generator->getBarcode(
                        (string) $product->barcode,
                        $generator::TYPE_CODE_128
                    ),
                    'barcode_number' => $product->barcode,
                ];
            });

        return view(
            'admin.products.print-barcodes',
            compact('products')
        );
    }
}