<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'image' => getFilePath($this->image),
            'category' => $this->category->name,
            'type' => $this->type == 1? 'portfolio':'inspiration',
            'details' => SpecialSectionDetailsResource::collection($this->whenLoaded('details'))->resolve(),
        ];
    }
}
