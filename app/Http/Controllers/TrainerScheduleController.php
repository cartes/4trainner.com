<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrainerScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('trainer.schedule', [
            'userName' => $user->name,
        ]);
    }
}
