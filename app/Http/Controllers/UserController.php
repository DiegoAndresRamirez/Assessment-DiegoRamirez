<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first(); 

        return Inertia::render('Dashboard', [
            'user' => $user,
            'role' => $role,
        ]);
    }}
