<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialSectionListResource extends JsonResource
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
            'title' => $this->title,
            'image' => getFilePath($this->image),
            'image2' => getFilePath($this->image2),
            'dismention' => getImageSize(getFilePath($this->image)),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'name' => $this->category->name,
                ];
            }),
        ];
    }
}
