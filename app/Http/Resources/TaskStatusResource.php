<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => (string)$this->id,
            "title" => $this->name,
            "color" => $this->color,
            "item" => TaskResource::collection($this->whenLoaded('tasks')),
            'system_default' => (bool)$this->system_default,
        ];
    }
}
