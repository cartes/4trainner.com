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
        $totalProfesores = User::role('profesor')->count();
        $totalAlumnos = User::role('alumno')->count();
        $totalModerators = User::role('moderador')->count();

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
