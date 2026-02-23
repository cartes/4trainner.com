<?php

namespace App\Http\Controllers;

use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Assigned routines with exercises
        $routines = $user->assignedRoutines()
            ->with(['exercises'])
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'description' => $r->description,
                'difficulty' => $r->difficulty,
                'exercises' => $r->exercises->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                ]),
                'is_active' => $r->pivot->is_active ?? true,
            ]);

        // Assigned trainers
        $trainers = $user->trainers()
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        // Progress records (last 30 days)
        $progress = StudentProgress::where('student_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'type' => $p->type,
                'value' => $p->value,
                'notes' => $p->notes,
                'date' => $p->date?->format('d/m/Y'),
            ]);

        // Goal progress: routines completed this month vs total assigned
        $totalAssigned = $routines->count();
        $completedThisMonth = $progress->where('type', 'session_completed')->count();
        $goalPercent = $totalAssigned > 0
            ? min(100, intval(($completedThisMonth / max($totalAssigned * 4, 1)) * 100))
            : 0;

        $dashboardData = json_encode([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'total_routines' => $routines->count(),
                'total_trainers' => $trainers->count(),
                'total_progress' => $progress->count(),
                'goal_percent' => $goalPercent,
            ],
            'routines' => $routines,
            'trainers' => $trainers,
            'progress' => $progress,
        ]);

        $authUser = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->values(),
        ]);

        return view('student.dashboard', compact('dashboardData', 'authUser'));
    }
}
