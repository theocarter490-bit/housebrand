<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BackgroundSettingsResource extends JsonResource
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
            'short_desc' => $this->short_desc,
            'type' => $this->type,
            'image' => asset(getFilePath($this->image)),
            'color' => $this->color
        ];
    }
}
