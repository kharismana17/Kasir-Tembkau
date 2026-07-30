<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedOrderItem extends Model
{
    protected $fillable = [
        'saved_order_id', 'product_id', 'name', 'price', 'qty', 'unit', 'sale_type', 'purchase_type', 'input_method', 'is_tembakau', 'subtotal',
    ];

    protected $casts = [
        'is_tembakau' => 'boolean',
    ];

    public function savedOrder(): BelongsTo
    {
        return $this->belongsTo(SavedOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
