<?php

namespace App\Http\Controllers\Api\V1\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Models\Exercise;
use App\Http\Resources\Api\V1\RoutineResource;
use App\Http\Resources\Api\V1\ExerciseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    /**
     * Display a listing of routines created by the trainer.
     */
    public function index(Request $request)
    {
        $routines = $request->user()->routines()
            ->with('exercises')
            ->latest()
            ->get();

        return RoutineResource::collection($routines);
    }

    /**
     * Store a newly created routine.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:Básico,Intermedio,Avanzado,Elite',
            'exercises' => 'required|array',
            'exercises.*.id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string',
            'exercises.*.rest_seconds' => 'nullable|integer',
            'exercises.*.notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $routine = $request->user()->routines()->create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'difficulty' => $validated['difficulty'],
            ]);

            foreach ($validated['exercises'] as $exerciseData) {
                $routine->exercises()->attach($exerciseData['id'], [
                    'sets' => $exerciseData['sets'],
                    'reps' => $exerciseData['reps'],
                    'rest_seconds' => $exerciseData['rest_seconds'] ?? 60,
                    'notes' => $exerciseData['notes'],
                ]);
            }

            return new RoutineResource($routine->load('exercises'));
        });
    }

    /**
     * Assign a routine to a student.
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'routine_id' => 'required|exists:routines,id',
            'student_id' => 'required|exists:users,id',
        ]);

        $routine = Routine::findOrFail($validated['routine_id']);

        // Check if student belongs to trainer (CRM logic)
        $isStudent = $request->user()->students()->where('student_id', $validated['student_id'])->exists();

        if (!$isStudent) {
            return response()->json(['message' => 'El alumno no está asignado a este entrenador.'], 403);
        }

        $routine->students()->syncWithoutDetaching([
            $validated['student_id'] => [
                'assigned_by' => $request->user()->id,
                'is_active' => true,
            ]
        ]);

        return response()->json(['message' => 'Rutina asignada con éxito al alumno.']);
    }

    /**
     * Get exercises catalog.
     */
    public function exercises()
    {
        return ExerciseResource::collection(Exercise::all());
    }
}
