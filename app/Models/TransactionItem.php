<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'qty',
        'price',
        'discount',
        'subtotal',
        'buy_price',
        'sell_price',
        'product_name',
        'product_unit',
        'product_category',
        'product_barcode',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
