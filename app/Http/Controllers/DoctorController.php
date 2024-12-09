<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index()
    {
        // Obtener el horario del doctor autenticado
        $schedule = Schedules::with('blocks')->where('doctor_id', auth()->id())->first();
    
        // Renderizar la vista del dashboard con los datos del horario
        return Inertia::render('Doctor/Dashboard', [
            'title' => 'Dashboard del Doctor',
            'description' => 'Gestiona tu horario y turnos.',
            'schedule' => $schedule,
        ]);
    }
}
