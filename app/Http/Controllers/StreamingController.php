<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use Illuminate\Support\Facades\Auth;

class StreamingController extends Controller
{
    /**
     * Display a listing of all available channels.
     */
    public function index()
    {
        $channels = Channel::with('user')->withCount(['videos'])->get();
        // Solo como base, podemos inyectar datos en la vista para Vue
        return view('student.channels', compact('channels'));
    }

    /**
     * Display a specific channel and its videos/stream.
     */
    public function show(Channel $channel)
    {
        // Load relationships (trainer info, videos history)
        $channel->load([
            'user',
            'videos' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('student.channel_view', compact('channel'));
    }
}
