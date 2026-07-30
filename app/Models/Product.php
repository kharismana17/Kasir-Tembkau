<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'description',
        'buy_price',
        'sell_price',
        'wholesale_price',
        'wholesale_min_qty',
        'stock',
        'stock_min',
        'sale_type',
        'unit',
        'stock_unit',
        'selling_unit',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'wholesale_min_qty' => 'integer',
        'stock' => 'integer',
        'stock_min' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockUnit()
    {
        if (! empty($this->stock_unit)) {
            return $this->stock_unit;
        }

        return self::resolveUnitsBySaleType($this->saleType())['stock_unit'];
    }

    public function sellingUnit()
    {
        if (! empty($this->selling_unit)) {
            return $this->selling_unit;
        }

        return self::resolveUnitsBySaleType($this->saleType())['selling_unit'];
    }

    /**
     * Price unit helpers
     * Current system stores `sell_price` as price per 100 gram (ons).
     * Provide a single source to obtain price per gram for UI and calculations.
     */
    public function priceUnit(): string
    {
        // Default to 'ons' (price per 100 gram) because server logic uses sell_price * (qty/100)
        return 'ons';
    }

    public function pricePerGram(): float
    {
        $price = (float) ($this->sell_price ?? 0);

        if ($this->priceUnit() === 'gram') {
            return $price;
        }

        // price per ons -> divide by 100
        return $price / 100.0;
    }

    public function saleType(): string
    {
        if (! empty($this->sale_type)) {
            return $this->sale_type;
        }

        return self::resolveSaleTypeByCategory($this->category_id);
    }

    public static function resolveUnitsByCategory($categoryId)
    {
        $saleType = self::resolveSaleTypeByCategory($categoryId);

        return self::resolveUnitsBySaleType($saleType);
    }

    public static function resolveSaleTypeByCategory($categoryId)
    {
        $category = Category::find($categoryId);
        $categoryName = strtolower(trim($category?->name ?? ''));

        if ($categoryName === 'tembakau') {
            return 'gram';
        }

        if (
            strpos($categoryName, 'pack') !== false ||
            strpos($categoryName, 'kemasan') !== false
        ) {
            return 'pack';
        }

        return 'pcs';
    }

    public static function resolveUnitsBySaleType(string $saleType)
    {
        $saleType = strtolower(trim($saleType));

        if (str_contains($saleType, 'gram')) {
            return [
                'stock_unit' => 'gram',
                'selling_unit' => 'gram',
            ];
        }

        if (str_contains($saleType, 'pack')) {
            return [
                'stock_unit' => 'pack',
                'selling_unit' => 'pack',
            ];
        }

        return [
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE SKU OTOMATIS
    |--------------------------------------------------------------------------
    */

    public static function generateUniqueSku(string $productName): string
    {
        $prefix = Str::of($productName)
            ->replaceMatches('/[^A-Za-z0-9\s]/', '')
            ->explode(' ')
            ->filter()
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->take(4)
            ->implode('');

        if (empty($prefix)) {
            $prefix = 'PRD';
        }

        do {
            $sku = $prefix . '-' . strtoupper(Str::random(6));
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE BARCODE EAN-13
    |--------------------------------------------------------------------------
    */

    public static function generateUniqueBarcode(): string
    {
        do {

            $base = '899' . str_pad(
                random_int(0, 999999999),
                9,
                '0',
                STR_PAD_LEFT
            );

            $barcode = $base . self::calculateEan13CheckDigit($base);

        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    private static function calculateEan13CheckDigit(string $barcode): int
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $barcode[$i];

            $sum += $i % 2 === 0
                ? $digit
                : $digit * 3;
        }

        return (10 - ($sum % 10)) % 10;
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}