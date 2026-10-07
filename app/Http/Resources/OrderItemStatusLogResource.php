<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemStatusLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date_time' => dateFormatwithTime($this->date_time),
            'note' => $this->note,
            'status' => new StatusResource($this->whenLoaded('status')),
        ];
    }
}
