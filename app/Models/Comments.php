<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'message',
        'date_hour',
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un comentario puede pertenecer a muchos appointments (muchos a muchos)
    public function appointments()
    {
        return $this->belongsToMany(Appointments::class, 'appointments__comments', 'comment_id', 'appointment_id');
    }

}
