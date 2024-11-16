<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = Schedules::with('blocks')->where('doctor_id', auth()->id())->first();
        return Inertia::render('Doctor/Dashboard', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'season' => 'required|string',
        ]);
    
        // Verificar si el doctor ya tiene un horario
        $existingSchedule = Schedules::where('doctor_id', auth()->id())->first();
    
        if ($existingSchedule) {
            // Devolver el horario existente
            return response()->json([
                'message' => 'Ya tienes un horario creado.',
                'schedule' => $existingSchedule,
            ], 200);
        }
    
        // Crear un nuevo horario si no existe
        $schedule = Schedules::create([
            'doctor_id' => auth()->id(),
            'season' => $request->season,
        ]);
    
        return response()->json($schedule, 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Schedules $schedules)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedules $schedules)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedules $schedules)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedules $schedules)
    {
        //
    }
}
