<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Return dashboard data for the authenticated student.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Load assigned routines with exercises
        $routines = $user->assignedRoutines()
            ->with(['exercises'])
            ->get();

        // Load trainers assigned to this student
        $trainers = $user->trainers()->select('users.id', 'users.name', 'users.email')->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'total_routines' => $routines->count(),
                'total_trainers' => $trainers->count(),
            ],
            'routines' => $routines,
            'trainers' => $trainers,
        ]);
    }
}
