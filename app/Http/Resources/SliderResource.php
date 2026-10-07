<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => match ($this->type) {
                0 => 'Hero Section',
                1 => 'About Us',
                2 => 'Banner with Product',
                default => 'Unknown',
            },
            'button_show' => $this->button_show,
            'image' => getFilePath($this->image),
            'products' => $this->when($this->type == 2, ProductListResource::collection($this->whenLoaded('products'))->resolve())
        ];
    }
}
