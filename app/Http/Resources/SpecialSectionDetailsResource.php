<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialSectionDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'section_type' => $this->section_type,
            'items' => SpecialSectionDetailsItemsResource::collection($this->whenLoaded('items'))->resolve(),
        ];
    }
}
