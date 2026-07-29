<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedOrderItem extends Model
{
    protected $fillable = [
        'saved_order_id',
        'product_id',
        'qty',
        'price',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(SavedOrder::class);
    }
}
