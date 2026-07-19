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
            compact('categories')
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | UNIT OTOMATIS BERDASARKAN KATEGORI
        |--------------------------------------------------------------------------
        */

        $units = Product::resolveUnitsByCategory(
            $data['category_id']
        );

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
        ]);


        /*
        |--------------------------------------------------------------------------
        | UNIT OTOMATIS
        |--------------------------------------------------------------------------
        */

        $units = Product::resolveUnitsByCategory(
            $data['category_id']
        );

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
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $message = $product->is_active
            ? 'Produk berhasil diaktifkan.'
            : 'Produk berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.products.index')
            ->with('success', $message);
    }


    /*
    |--------------------------------------------------------------------------
    | BARCODE IMAGE
    |--------------------------------------------------------------------------
    */

    public function barcode(Product $product)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();

        $barcode = $generator->getBarcode(
            $product->barcode,
            $generator::TYPE_EAN_13
        );

        return response($barcode)
            ->header(
                'Content-Type',
                'image/svg+xml'
            );
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

        $barcode = $generator->getBarcode(
            $product->barcode,
            $generator::TYPE_EAN_13
        );

        $products = collect(
            array_fill(0, 24, [
                'product' => $product,
                'barcode' => $barcode,
            ])
        );

        return view(
            'admin.products.print-barcode',
            compact('products')
        );
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
            ->get();

        $products = $products->map(function ($product) use ($generator) {

            $barcode = $this->generateEan13($product);

            return [
                'product' => $product,
                'barcode' => $generator->getBarcode(
                    $barcode,
                    $generator::TYPE_EAN_13
                ),
                'barcode_number' => $barcode,
            ];

        });

        return view('admin.products.print-barcodes', compact('products'));
    }



    private function generateEan13(Product $product): string
    {
        $base = str_pad(
            (string) $product->id,
            12,
            '0',
            STR_PAD_LEFT
        );

        $digits = str_split($base);

        $sum = 0;

        foreach ($digits as $index => $digit) {

            $sum += ((int) $digit) * ($index % 2 === 0 ? 1 : 3);

        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $base . $checkDigit;
    }
}