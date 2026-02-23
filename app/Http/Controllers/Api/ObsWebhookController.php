<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObsWebhookController extends Controller
{
    /**
     * Webhook triggered by Nginx-RTMP or Node-Media-Server
     * when OBS starts a stream.
     */
    public function onPublish(Request $request)
    {
        // RTMP servers typically send the stream key as 'name' or 'key' parameter
        $streamKey = $request->input('name') ?? $request->input('key');

        if (!$streamKey) {
            Log::warning('OBS Webhook: Connection attempt without stream key.');
            return response('Stream key is required', 400);
        }

        $channel = Channel::where('stream_key', $streamKey)->first();

        if (!$channel) {
            Log::warning("OBS Webhook: Rejected invalid stream key: {$streamKey}");
            // Returning 404 or 403 will tell the RTMP server to reject the connection
            return response('Invalid stream key', 403);
        }

        // Key is valid. Set channel to live.
        $channel->update(['is_live' => true]);

        // Create a new Video record for this live session
        $video = new Video([
            'title' => 'Transmisión en Vivo: ' . date('Y-m-d H:i:s'),
            'status' => 'live',
            // File path might be constructed later or managed by the RTMP server
            'file_path' => 'streaming/live/' . $streamKey . '.m3u8',
            'thumbnail_path' => $channel->cover_image, // Or default
        ]);

        $channel->videos()->save($video);

        Log::info("OBS Webhook: Channel {$channel->name} is now LIVE.");

        // Return 200 OK to allow the stream
        return response('OK', 200);
    }

    /**
     * Webhook triggered when OBS stops streaming.
     */
    public function onPublishDone(Request $request)
    {
        $streamKey = $request->input('name') ?? $request->input('key');

        if (!$streamKey) {
            return response('Stream key is required', 400);
        }

        $channel = Channel::where('stream_key', $streamKey)->first();

        if ($channel) {
            $channel->update(['is_live' => false]);

            // Find the current live video and set it to VOD or processing
            $liveVideo = $channel->videos()->where('status', 'live')->latest()->first();
            if ($liveVideo) {
                $liveVideo->update(['status' => 'processing']);
            }

            Log::info("OBS Webhook: Channel {$channel->name} stream ENDED.");
        }

        return response('OK', 200);
    }
}
