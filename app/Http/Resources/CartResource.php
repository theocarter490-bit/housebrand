<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->id,
            'product' => new ProductListResource($this->whenLoaded('product')),
            'variation' => $this->variation,
            'price' =>$this->price,
            'quantity' =>$this->quantity,
            'tag' => $this->tag,
        ];
    }
}
