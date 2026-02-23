<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->count();

        $totalAlumnos = User::role('alumno')->count();
        $totalProfesores = User::role('profesor')->count();
        $totalModerators = User::role('moderador')->count();
        $totalRoutines = \App\Models\Routine::count();

        $newUsersPerDay = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->roles->pluck('name'),
                'created_at' => $u->created_at->diffForHumans(),
            ]);

        $dashboardData = json_encode([
            'stats' => [
                'total_users' => $totalUsers,
                'total_alumnos' => $totalAlumnos,
                'total_profesores' => $totalProfesores,
                'total_moderators' => $totalModerators,
                'total_routines' => $totalRoutines,
            ],
            'new_users_per_day' => $newUsersPerDay,
            'recent_users' => $recentUsers,
        ]);

        return view('admin.dashboard', compact('dashboardData'));
    }


    public function users()
    {
        // Now returns the new Vue-based blade
        return view('admin.vue_users');
    }

    // Ajax endpoint to feed the Vue component with all non-super-admin users
    public function data(Request $request)
    {
        $users = User::with('meta')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super-admin');
            })->latest()->get()->map(function ($user) {
                $phoneMeta = $user->meta->where('meta_key', 'phone')->first();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $phoneMeta ? $phoneMeta->meta_value : '',
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => clone $user->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $user->created_at->diffForHumans(),
                ];
            });

        return response()->json($users);
    }

    // Store via JSON API
    public function store(Request $request)
    {
        $val = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            if ($request->has('phone') && !empty($request->phone)) {
                $user->meta()->create([
                    'meta_key' => 'phone',
                    'meta_value' => $request->phone,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'User created successfully', 'user' => $user]);
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error creating user'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $val = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $user->syncRoles([$request->role]);

            $phoneMeta = $user->meta()->firstOrCreate(['meta_key' => 'phone']);
            $phoneMeta->update(['meta_value' => $request->phone]);

            return response()->json(['success' => true, 'message' => 'User updated successfully']);

        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Error updating user'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Error deleting user'], 500);
        }
    }
}
