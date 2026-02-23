<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    /**
     * Display a listing of messages for the specified video.
     */
    public function index(Video $video)
    {
        // Get the latest 100 messages, ordered chronologically (oldest first).
        $messages = $video->chatMessages()
            ->with([
                'user' => function ($query) {
                    // Select only necessary fields and eager-load roles
                    $query->select('id', 'name', 'email')->with('roles');
                }
            ])
            ->latest()
            ->take(100)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'data' => $messages
        ]);
    }

    /**
     * Store a newly created chat message for the specified video.
     */
    public function store(Request $request, Video $video)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = new ChatMessage([
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        $video->chatMessages()->save($message);

        // Load user info for real-time appending on frontend
        $message->load([
            'user' => function ($query) {
                $query->select('id', 'name', 'email')->with('roles');
            }
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }
}
