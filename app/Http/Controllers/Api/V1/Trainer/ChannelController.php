<?php

namespace App\Http\Controllers\Api\V1\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ChannelController extends Controller
{
    /**
     * GET /api/v1/trainer/channel
     * Returns the trainer's channel with stream credentials and VOD list.
     */
    public function show(Request $request): JsonResponse
    {
        $channel = $request->user()->channels()->with([
            'videos' => fn ($q) => $q->orderByDesc('created_at'),
        ])->first();

        if (! $channel) {
            return response()->json(['message' => 'No tienes un canal asignado.'], 404);
        }

        return response()->json([
            'channel' => array_merge($channel->toArray(), [
                'rtmp_url' => 'rtmp://stream.4trainner.com/live',
            ]),
        ]);
    }

    /**
     * GET /api/v1/trainer/channel/status
     * Lightweight endpoint for live-status polling.
     */
    public function status(Request $request): JsonResponse
    {
        $channel = $request->user()->channels()->first();

        if (! $channel) {
            return response()->json(['message' => 'No tienes un canal asignado.'], 404);
        }

        $videoCount = $channel->videos()->whereIn('status', ['vod', 'processing'])->count();

        return response()->json([
            'is_live'     => (bool) $channel->is_live,
            'video_count' => $videoCount,
        ]);
    }

    /**
     * POST /api/v1/trainer/channel/videos
     * Upload a VOD video to the trainer's channel.
     */
    public function uploadVideo(Request $request): JsonResponse
    {
        $channel = $request->user()->channels()->first();

        if (! $channel) {
            return response()->json(['message' => 'No tienes un canal asignado.'], 404);
        }

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video'       => ['required', File::types(['mp4', 'mov', 'avi', 'mkv'])->max(2 * 1024 * 1024)],
        ]);

        $file     = $request->file('video');
        $path     = $file->store("videos/channel-{$channel->id}", 'public');
        $size     = $file->getSize();

        $video = $channel->videos()->create([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'status'      => 'vod',
            'file_path'   => $path,
            'size'        => $size,
        ]);

        return response()->json(['video' => $video], 201);
    }

    /**
     * DELETE /api/v1/trainer/channel/videos/{video}
     * Delete a VOD video owned by this trainer's channel.
     */
    public function destroyVideo(Request $request, Video $video): JsonResponse
    {
        $channel = $request->user()->channels()->first();

        if (! $channel || $video->channel_id !== $channel->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($video->file_path) {
            Storage::disk('public')->delete($video->file_path);
        }

        $video->delete();

        return response()->json(['message' => 'Video eliminado correctamente.']);
    }
}
