<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\ScheduledClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * GET /api/v1/student/schedule
     * Returns upcoming classes from channels owned by the student's trainers.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get trainer IDs for this student
        $trainerIds = $user->trainers()->pluck('users.id');

        if ($trainerIds->isEmpty()) {
            return response()->json([]);
        }

        // Get channels owned by those trainers
        $channelIds = \App\Models\Channel::whereIn('user_id', $trainerIds)->pluck('id');

        if ($channelIds->isEmpty()) {
            return response()->json([]);
        }

        $classes = ScheduledClass::upcoming()
            ->whereIn('channel_id', $channelIds)
            ->with(['channel:id,name', 'trainer:id,name'])
            ->get();

        return response()->json($classes);
    }
}
