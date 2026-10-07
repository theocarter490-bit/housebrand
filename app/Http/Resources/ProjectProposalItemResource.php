<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectProposalItemResource extends JsonResource
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
            'name' => $this->product ? $this->product->name : $this->service->title,
            'image' => $this->product ? getFilePath($this->product->thumbnail_img) : getFilePath($this->service->image),
            'unit_price' => getPriceFormat($this->unit_price),
            'quantity' => $this->quantity,
            'total_price' => getPriceFormat($this->price),
            'variation' => $this->variation ?? [],
            'type' => $this->type == 1 ? 'Product' : 'Service',
        ];
    }
}
