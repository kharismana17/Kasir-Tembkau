<?php

namespace App\Models;

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
        'stock',
        'stock_min',
        'unit',
        'stock_unit',
        'selling_unit',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
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
        return $this->stock_unit ?? 'pcs';
    }

    public function sellingUnit()
    {
        return $this->selling_unit ?? 'pcs';
    }

    public static function resolveUnitsByCategory($categoryId)
    {
        $category = Category::find($categoryId);
        $categoryName = strtolower(trim($category?->name ?? ''));

        $stockUnit = 'pcs';
        $sellingUnit = 'pcs';

        if ($categoryName === 'tembakau') {
            $stockUnit = 'gram';
            $sellingUnit = 'gram';
        } elseif (
            strpos($categoryName, 'pack') !== false ||
            strpos($categoryName, 'kemasan') !== false
        ) {
            $stockUnit = 'pack';
            $sellingUnit = 'pack';
        }

        return [
            'stock_unit' => $stockUnit,
            'selling_unit' => $sellingUnit,
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