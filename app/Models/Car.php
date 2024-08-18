<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand',
        'model',
        'license_plate',
        'daily_rate',
        'available',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function getAvailableAttribute()
    {
        return $this->attributes['available'] ? 'Tersedia' : 'Disewa';
    }
}
