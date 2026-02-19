<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone ?? '+56900000000', // Asumiendo que existe un campo phone o se manejará
            'subscription_status' => $this->subscription_status ?? 'Activo',
            'last_activity' => $this->updated_at->diffForHumans(),
        ];
    }
}
