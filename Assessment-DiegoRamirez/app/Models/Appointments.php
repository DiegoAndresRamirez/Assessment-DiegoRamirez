<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    protected $fillable = [
        'reason',
        'status',
        'speciality',
        'patient_id',
        'doctor_id',
        'block_id',
    ];

    public function patient() 
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Relación: Un appointment pertenece a un doctor (usuario)
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Relación: Un appointment pertenece a un block
    public function block()
    {
        return $this->belongsTo(Blocks::class, 'block_id');
    }

    // Relación: Un appointment puede tener muchos comentarios (muchos a muchos)
    public function comments()
    {
        return $this->belongsToMany(Comments::class, 'appointments__comments', 'appointment_id', 'comment_id');
    }
}
