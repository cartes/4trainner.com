<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Trainer;

use App\Http\Controllers\Controller;
use App\Models\ScheduledClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /** GET /api/v1/trainer/schedule — list all classes for the trainer's channels. */
    public function index(Request $request): JsonResponse
    {
        $user    = $request->user();
        $channel = $user->channels()->first();

        if (! $channel) {
            return response()->json(['message' => 'No tienes un canal asignado.'], 404);
        }

        $classes = ScheduledClass::byChannel($channel->id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($classes);
    }

    /** POST /api/v1/trainer/schedule — create a new scheduled class. */
    public function store(Request $request): JsonResponse
    {
        $user    = $request->user();
        $channel = $user->channels()->first();

        if (! $channel) {
            return response()->json(['message' => 'No tienes un canal asignado.'], 404);
        }

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'scheduled_at'     => ['required', 'date', 'after:now'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'max_students'     => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $class = ScheduledClass::create([
            ...$data,
            'channel_id' => $channel->id,
            'trainer_id' => $user->id,
            'duration_minutes' => $data['duration_minutes'] ?? 60,
            'status'     => 'scheduled',
        ]);

        return response()->json($class, 201);
    }

    /** PATCH /api/v1/trainer/schedule/{scheduledClass} — update a class. */
    public function update(Request $request, ScheduledClass $scheduledClass): JsonResponse
    {
        $user = $request->user();

        if ($scheduledClass->trainer_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'title'            => ['sometimes', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'scheduled_at'     => ['sometimes', 'date'],
            'duration_minutes' => ['sometimes', 'integer', 'min:15', 'max:480'],
            'max_students'     => ['nullable', 'integer', 'min:1', 'max:500'],
            'status'           => ['sometimes', Rule::in(['scheduled', 'live', 'ended', 'cancelled'])],
        ]);

        $scheduledClass->update($data);

        return response()->json($scheduledClass->fresh());
    }

    /** DELETE /api/v1/trainer/schedule/{scheduledClass} — cancel a class. */
    public function destroy(Request $request, ScheduledClass $scheduledClass): JsonResponse
    {
        $user = $request->user();

        if ($scheduledClass->trainer_id !== $user->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $scheduledClass->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Clase cancelada.']);
    }
}
