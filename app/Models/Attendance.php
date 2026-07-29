<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_user_id',
        'attendance_date',
        'check_in',
        'check_out',
        'working_minutes',
        'status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'working_minutes' => 'integer',
    ];

    public function user()
    {
        return null;
    }

    public function attendanceUser()
    {
        return $this->belongsTo(AttendanceUser::class, 'attendance_user_id');
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            'working' => 'Sedang Bekerja',
            'completed' => 'Sudah Pulang',
            default => 'Belum Check In',
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = max(0, (int) ($this->working_minutes ?? 0));
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return sprintf('%d Jam %d Menit', $hours, $remainingMinutes);
        }

        if ($hours > 0) {
            return sprintf('%d Jam', $hours);
        }

        if ($remainingMinutes > 0) {
            return sprintf('%d Menit', $remainingMinutes);
        }

        return '0 Menit';
    }
}
