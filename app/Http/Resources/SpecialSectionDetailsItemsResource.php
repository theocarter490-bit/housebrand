<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialSectionDetailsItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'  => $this->title ?? null,
            'description' => $this->description ?? null,
            'amount' => $this->amount ?? null,
            'image' => $this->image ? getFilePath($this->image) : null,
            'dimensions' => $this->image ? getImageSize(getFilePath($this->image)) : [],
            'products' => ProductListResource::collection($this->whenLoaded('products'))->resolve()
        ];
    }
}
