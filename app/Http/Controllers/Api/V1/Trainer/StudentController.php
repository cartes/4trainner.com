<?php

namespace App\Http\Controllers\Api\V1\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;

use App\Http\Resources\Api\V1\StudentResource;

class StudentController extends Controller
{
    /**
     * Display a listing of students assigned to the trainer.
     */
    public function index(Request $request)
    {
        $students = $request->user()->students;

        return StudentResource::collection($students);
    }

    /**
     * Return dashboard stats for the authenticated trainer.
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $totalStudents = $user->students()->count();
        $totalRoutines = $user->routines()->count();
        $totalExercises = Exercise::count();
        $recentStudents = $user->students()->orderByPivot('created_at', 'desc')->take(5)->get();
        $recentRoutines = $user->routines()->with('exercises')->latest()->take(5)->get();
        $assignedRoutinesCount = $user->routines()
            ->has('students')
            ->count();

        return response()->json([
            'stats' => [
                'total_students' => $totalStudents,
                'total_routines' => $totalRoutines,
                'total_exercises' => $totalExercises,
                'assigned_routines' => $assignedRoutinesCount,
            ],
            'recent_students' => StudentResource::collection($recentStudents),
            'recent_routines' => $recentRoutines->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'difficulty' => $r->difficulty,
                'exercises_count' => $r->exercises->count(),
                'created_at' => $r->created_at->diffForHumans(),
            ]),
        ]);
    }
}
