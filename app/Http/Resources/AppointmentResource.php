<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'slot_id' => $this->slot_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'slot' => new TimeSlotResource($this->whenLoaded('slot')),
        ];
    }
}
