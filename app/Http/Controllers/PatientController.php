<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        // Obtener los doctores disponibles para el paciente
        $doctors = User::role('doctor')->get();

        // Renderizar la vista con los doctores
        return Inertia::render('Patient/Dashboard', [
            'title' => 'Dashboard del Paciente',
            'description' => 'Gestiona tus citas medicas.',
            'doctors' => $doctors,  
        ]);
    }

    public function getDoctors()
    {
        // Obtener los doctores con el rol 'doctor'
        $doctors = User::role('doctor')->get();

        // Devolver los doctores como respuesta JSON
        return response()->json($doctors);
    }
}
