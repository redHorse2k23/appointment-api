<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtSchedules extends Model
{
    use HasFactory;


    protected $fillable = [
        'court_id',
        'date',
        'start_time',
        'end_time',
    ];

    public function court()
    {
        return $this->belongsTo(Courts::class, 'court_id');
    }



}

