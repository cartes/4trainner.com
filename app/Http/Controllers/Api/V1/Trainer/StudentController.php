<?php

namespace App\Http\Controllers\Api\V1\Trainer;

use App\Http\Controllers\Controller;
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
}
