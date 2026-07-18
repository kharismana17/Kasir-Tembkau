<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            $sellingUnit = 'ons';
        } elseif (strpos($categoryName, 'pack') !== false || strpos($categoryName, 'kemasan') !== false) {
            $stockUnit = 'pack';
            $sellingUnit = 'pack';
        }

        return [
            'stock_unit' => $stockUnit,
            'selling_unit' => $sellingUnit,
        ];
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
