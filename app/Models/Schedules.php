<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Blocks;

class Schedules extends Model
{
    protected $fillable = [
        'season',
        'doctor_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function blocks()
    {
        return $this->hasMany(Blocks::class, 'schedule_id');
    }
}
