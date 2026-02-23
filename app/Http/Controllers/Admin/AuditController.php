<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Routine;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Show the audit and metrics SPA view
     */
    public function index()
    {
        return view('admin.vue_audit');
    }

    /**
     * Return paginated activity logs for the data table
     */
    public function logs(Request $request)
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate($request->get('per_page', 20));

        // Format for the frontend
        $formatted = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'description' => $log->description,
                'log_name' => collect(explode('\\', $log->subject_type))->last() ?? $log->log_name,
                'subject_id' => $log->subject_id,
                'causer_name' => $log->causer ? $log->causer->name : 'Sistema / Anónimo',
                'causer_email' => $log->causer ? $log->causer->email : '',
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => clone $log->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'data' => $formatted,
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'total' => $logs->total()
        ]);
    }

    /**
     * Return general system metrics
     */
    public function metrics()
    {
        $todayStart = Carbon::today();

        $metrics = [
            'total_users' => User::count(),
            'new_users_today' => User::where('created_at', '>=', $todayStart)->count(),

            'total_routines' => Routine::count(),
            'new_routines_today' => Routine::where('created_at', '>=', $todayStart)->count(),

            'actions_today' => Activity::where('created_at', '>=', $todayStart)->count(),
            'total_actions' => Activity::count(),

            // Estimations for display
            'db_size_mb' => $this->getDatabaseSizeEstimate(),
        ];

        return response()->json($metrics);
    }

    /**
     * Naive estimation of DB size based on DB driver currently in use.
     * Often complex in shared hostings, returning a mock or simple query for SQLite/MySQL.
     */
    private function getDatabaseSizeEstimate()
    {
        $connection = config('database.default');

        try {
            if ($connection === 'sqlite') {
                $path = config('database.connections.sqlite.database');
                if (file_exists($path)) {
                    return round(filesize($path) / 1048576, 2);
                }
            } elseif ($connection === 'mysql') {
                $dbName = config('database.connections.mysql.database');
                $result = \DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema='$dbName'");
                return round($result[0]->size ?? 0, 2);
            } elseif ($connection === 'pgsql') {
                $result = \DB::select("SELECT pg_database_size(current_database())/1024/1024 as size");
                return round($result[0]->size ?? 0, 2);
            }
        } catch (\Exception $e) {
            return 0;
        }

        return 0; // Default if undetermined
    }
}
