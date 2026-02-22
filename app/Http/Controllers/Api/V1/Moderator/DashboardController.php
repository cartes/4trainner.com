<?php

namespace App\Http\Controllers\Api\V1\Moderator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Return dashboard data for the moderator.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();

        // Optimize: Get all role counts in a single query
        $roleCounts = Role::whereIn('name', ['profesor', 'alumno', 'moderador'])
            ->withCount('users')
            ->get()
            ->pluck('users_count', 'name');

        $totalProfesores = $roleCounts['profesor'] ?? 0;
        $totalAlumnos = $roleCounts['alumno'] ?? 0;
        $totalModerators = $roleCounts['moderador'] ?? 0;

        // Recent users (last 10 registered)
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
            'recent_users' => $recentUsers,
        ]);
    }
}
