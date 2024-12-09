<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blocks extends Model
{
    protected $fillable = [
        'hour_start',
        'hour_end',
        'disponibility',
        'schedule_id',
        'day',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedules::class, 'schedule_id');
    }

    public function appointment()
    {
        return $this->hasOne(Appointments::class, 'block_id');
    }

    
}
