<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'muscle_group' => $this->muscle_group,
            'video_url' => $this->video_url,
            // Pivot data if available
            'pivot' => $this->whenPivotLoaded('routine_exercise', function () {
                return [
                    'sets' => $this->pivot->sets,
                    'reps' => $this->pivot->reps,
                    'rest_seconds' => $this->pivot->rest_seconds,
                    'notes' => $this->pivot->notes,
                ];
            }),
        ];
    }
}
