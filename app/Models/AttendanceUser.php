<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceUser extends Model
{
    use HasFactory;

    protected $table = 'attendance_users';

    protected $fillable = [
        'name',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'attendance_user_id');
    }
}
