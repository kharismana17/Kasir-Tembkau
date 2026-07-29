<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedOrder extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'total',
        'total_items'
    ];

    public function items()
    {
        return $this->hasMany(SavedOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
