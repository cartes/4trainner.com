<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return global KPI data for the super-admin dashboard.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalProfesores = User::role('profesor')->count();
        $totalAlumnos = User::role('alumno')->count();
        $totalModerators = User::role('moderador')->count();

        // Users registered in the last 30 days grouped by day
        $newUsersPerDay = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent users (last 10)
        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->getRoleNames(),
                'created_at' => $u->created_at->diffForHumans(),
            ]);

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'total_profesores' => $totalProfesores,
                'total_alumnos' => $totalAlumnos,
                'total_moderators' => $totalModerators,
            ],
            'new_users_per_day' => $newUsersPerDay,
            'recent_users' => $recentUsers,
        ]);
    }
}
