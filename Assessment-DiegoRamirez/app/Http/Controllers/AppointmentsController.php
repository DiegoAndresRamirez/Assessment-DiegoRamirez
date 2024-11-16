<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Blocks;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener las citas del paciente autenticado
        $appointments = Appointments::where('patient_id', auth()->id())->with('doctor', 'block')->get();
        return response()->json($appointments);
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
        // Validar los datos
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'block_id' => 'required|exists:blocks,id',
            'reason' => 'required|string',
            'speciality' => 'required|string',
        ]);

        // Crear la cita
        $appointment = Appointments::create([
            'reason' => $request->reason,
            'speciality' => $request->speciality,
            'status' => 'pending',  // Estatus inicial
            'patient_id' => auth()->id(),
            'doctor_id' => $request->doctor_id,
            'block_id' => $request->block_id,
        ]);

        // Redirigir a la vista con un mensaje de éxito
        return redirect()->route('patient.dashboard')->with('success', 'Cita creada con éxito');
    }

    public function getBlocksForDoctor($doctorId)
    {
        // Obtener los bloques del doctor seleccionado
        $blocks = Blocks::whereHas('schedule', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId);
        })->get();

        // Devolver los bloques como respuesta JSON
        return response()->json($blocks);
    }

    public function getDoctorAppointments($doctorId)
    {
        $appointments = Appointments::where('doctor_id', $doctorId)
            ->with('patient', 'block')  // Incluir datos de paciente y bloque
            ->get();
        return response()->json($appointments);
    }

    public function changeStatus($appointmentId, Request $request)
    {
        $appointment = Appointments::findOrFail($appointmentId);
        $appointment->status = $request->status;
        $appointment->save();

        return response()->json($appointment);
    }


    /**
     * Display the specified resource.
     */
    public function show(Appointments $appointments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointments $appointments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointments $appointments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointments $appointments)
    {
        //
    }
}
