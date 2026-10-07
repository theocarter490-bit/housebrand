<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenLoaded('product', fn() => $this->product->id),
            'wishlist_id' => $this->id,
            'slug' => $this->whenLoaded('product', fn() => $this->product->slug),
            'name' => $this->whenLoaded('product', fn() => $this->product->name),
            'price' => $this->whenLoaded('product', fn() => $this->product->unit_price),
            'image' => $this->whenLoaded('product', fn() => getFilePath($this->product->thumbnail_img)),
            'shop_slug' => $this->whenLoaded('product', fn() => $this->product->shop->slug),
            'seller_id' => $this->whenLoaded('product', fn() => $this->product->user_id),
        ];
    }
}
