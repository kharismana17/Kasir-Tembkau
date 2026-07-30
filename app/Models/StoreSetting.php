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
        'hourly_salary',
        // Stock
        'default_stock_min',
        'notify_low_stock',
    ];

    protected $casts = [
        'tax_percentage' => 'decimal:2',
        'rounding' => 'integer',
        'hourly_salary' => 'decimal:2',
        'default_stock_min' => 'integer',
        'notify_low_stock' => 'boolean',
    ];
}
