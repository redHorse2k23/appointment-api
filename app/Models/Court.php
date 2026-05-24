<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'location',
        'court_number',
        'type',
        'hourly_rate',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'court_id');
    }

}
