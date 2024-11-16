<?php

namespace App\Http\Controllers;

use App\Models\Blocks;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'hour_start' => 'required|date_format:H:i',
            'hour_end' => 'required|date_format:H:i|after:hour_start',
            'day' => 'required|date',
            'disponibility' => 'required|in:available,unavailable',
        ]);
    
        $block = Blocks::create([
            'schedule_id' => $validated['schedule_id'],
            'hour_start' => $validated['hour_start'],
            'hour_end' => $validated['hour_end'],
            'day' => $validated['day'],
            'disponibility' => $validated['disponibility'],
        ]);
    
        // Solo devolver el bloque creado y el schedule actualizado
        return response()->json([
            'block' => $block,
            'schedule' => $block->schedule()->with('blocks')->first()
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Blocks $blocks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blocks $blocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blocks $blocks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blocks $blocks)
    {
        //
    }
}
