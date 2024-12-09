<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments_Comments extends Model
{
    protected $fillable = [
        'appointment_id',
        'comment_id',
        'date'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointments::class, 'appointment_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comments::class, 'comment_id');
    }
}
