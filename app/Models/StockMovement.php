<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'change',
        'type',
        'reference_type',
        'reference_id',
        'user_id',
        'note',
    ];

    protected $casts = [
        'change' => 'integer',
        'reference_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unitLabel()
    {
        $categoryName = strtolower(trim($this->product?->category?->name ?? ''));

        if ($categoryName === 'tembakau') {
            return 'gram';
        }

        return $this->product?->unit ?: 'pcs';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}