<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtSchedule extends Model
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
        return $this->belongsTo(Court::class, 'court_id');
    }



}

