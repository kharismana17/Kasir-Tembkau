<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'total_sessions',
        'total_hours',
        'hourly_rate',
        'total_salary',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_sessions' => 'integer',
        'total_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function attendanceUser()
    {
        return $this->belongsTo(AttendanceUser::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'paid' ? 'Sudah Dibayar' : 'Draft';
    }
}
