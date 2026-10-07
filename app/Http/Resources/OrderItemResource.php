<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sellerSlug = null;
        if ($this->product->user->role_id == 3) {
            $sellerSlug = optional($this->product->user->shop)->slug;
        }
        return [
            'id' => $this->id,
            'product_id' => $this->whenLoaded('product', $this->product->id),
            'slug' => $this->whenLoaded('product', $this->product->slug),
            'name' => $this->whenLoaded('product', $this->product->name),
            'image' => $this->whenLoaded('product', getFilePath($this->product->thumbnail_img)),
            'variation' => $this->variation,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'price' => $this->price,
            'status_log' => OrderItemStatusLogResource::collection($this->whenLoaded('statusLog')),
            'seller' => $sellerSlug,
        ];
    }
}
