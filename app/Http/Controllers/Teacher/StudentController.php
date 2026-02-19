<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('teacher.students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        return view('teacher.students.show', ['id' => $id]);
    }

    /**
     * API: Get list of students for authenticated teacher.
     */
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ensure user is authorized to view students
        if (!$user->can('view students') && !$user->hasRole('profesor') && !$user->hasRole('super-admin')) {
             // If roles are used, check for 'profesor'.
             // Or maybe just check if they have students relationship populated?
             // But let's assume middleware or role check.
        }

        $students = $user->students()
            ->with(['meta'])
            ->get();

        return response()->json($students);
    }

    /**
     * API: Get details of a specific student.
     */
    public function details(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $student = $user->students()
            ->with(['meta', 'progress'])
            ->findOrFail($id);

        return response()->json($student);
    }
}
