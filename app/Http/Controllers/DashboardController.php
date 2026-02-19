<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect the authenticated user to their role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        \Log::info('Dashboard redirect check', ['user' => $user->email, 'roles' => $user->getRoleNames()]);

        if ($user->hasRole('super-admin')) {
            \Log::info('Redirecting to admin dashboard');
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('profesor')) {
            \Log::info('Redirecting to trainer dashboard');
            return redirect()->route('trainer.dashboard');
        }

        if ($user->hasRole('moderador')) {
            \Log::info('Redirecting to moderator dashboard');
            return redirect()->route('moderator.dashboard');
        }

        if ($user->hasRole('alumno') || $user->hasRole('student')) {
            \Log::info('Redirecting to student dashboard');
            return redirect()->route('student.dashboard');
        }

        \Log::info('No specific role found, showing generic dashboard');
        return view('dashboard');
    }
}
