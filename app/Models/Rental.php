<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'estimated_cost',
        'duration',
        'is_returned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function carReturn()
    {
        return $this->hasOne(CarReturn::class);
    }

    public function getIsReturnedAttribute()
    {
        return $this->attributes['is_returned'] ? 'Dikembalilkan' : 'Disewa';
    }
}
