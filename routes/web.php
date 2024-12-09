<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\BlocksController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\UserController;
use App\Models\Schedules;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('doctor')) {
        // Si necesitas cargar el schedule u otros datos específicos del doctor hazlo aquí
        $schedule = Schedules::where('doctor_id', $user->id)->with('blocks')->first();

        return Inertia::render('Dashboard', [
            'title' => 'Dashboard del Doctor',
            'description' => 'Gestiona tus citas',
            'schedule' => $schedule,
        ]);
    } elseif ($user->hasRole('patient')) {
        // Datos específicos del paciente (si los necesitas)
        return Inertia::render('Dashboard', [
            'title' => 'Dashboard del Paciente',
            'description' => 'Agenda tus citas',
        ]);
    }
})->name('dashboard');

Route::middleware(['auth', 'role:doctor'])->get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::post('/schedules', [SchedulesController::class, 'store'])->name('schedules.store');
    Route::post('/blocks', [BlocksController::class, 'store'])->name('blocks.store');
    Route::get('/appointments/doctor/{doctorId}', [AppointmentsController::class, 'getDoctorAppointments']);
    Route::patch('/appointments/{appointmentId}/status', [AppointmentsController::class, 'changeStatus']);

});

Route::middleware(['auth', 'role:patient'])->get('/patient/dashboard', [PatientController::class, 'index'])->name('patient.dashboard');
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/appointments/blocks/{doctorId}', [AppointmentsController::class, 'getBlocksForDoctor']);
    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::get('/doctors', [PatientController::class, 'getDoctors']);
    Route::get('/appointments', [AppointmentsController::class, 'index']);
});