<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrainerStudioController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $channel = $user->channels()->with([
            'videos' => function ($query) {
                $query->latest()->take(1);
            }
        ])->first();

        if (!$channel) {
            return redirect()->route('trainer.dashboard')->with('error', 'No tienes un canal asignado.');
        }

        return view('trainer.studio', compact('channel'));
    }
}
