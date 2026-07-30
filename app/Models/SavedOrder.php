<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavedOrder extends Model
{
    protected $fillable = [
        'user_id', 'name', 'subtotal', 'tax_amount', 'total', 'total_items',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SavedOrderItem::class);
    }
}