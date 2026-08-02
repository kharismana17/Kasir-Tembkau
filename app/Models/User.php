<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\CashierUnit;
use App\Models\Location;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'is_active',
        'cashier_unit_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function cashierUnit()
    {
        return $this->belongsTo(CashierUnit::class);
    }

    public function currentLocation(): ?Location
    {
        return $this->cashierUnit?->location;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function setPin(string $pin)
    {
        $this->pin_hash = \Illuminate\Support\Facades\Hash::make($pin);
        $this->save();
    }

    public function checkPin(string $pin): bool
    {
        if (! $this->pin_hash) return false;
        return \Illuminate\Support\Facades\Hash::check($pin, $this->pin_hash);
    }
}
