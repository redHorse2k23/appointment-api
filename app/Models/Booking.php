<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'court_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'attachment',
        'reference_number',
        'notes',
        'cancelled_by',
        'cancelled_at',
    ];

    public function getUserIdAttribute()
    {
        return $this->attributes['user_id_1'] ?? null;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id_1'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'booking_id');
    }

}
