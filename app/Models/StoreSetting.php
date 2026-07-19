<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'address',
        'phone',
        'logo_path',
        // Transaction
        'tax_percentage',
        'rounding',
        'transaction_number_format',
        // Stock
        'default_stock_min',
        'notify_low_stock',
    ];

    protected $casts = [
        'tax_percentage' => 'decimal:2',
        'rounding' => 'integer',
        'default_stock_min' => 'integer',
        'notify_low_stock' => 'boolean',
    ];
}
