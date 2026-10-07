<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SharedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sellerSlug = null;
        if ($this->product->role_id == 3) {
            $sellerSlug = optional($this->product->shop)->slug;
        }
        return [
            'id' => $this->id,
            'name' => $this->whenLoaded('product', $this->product->name),
            'image' => $this->whenLoaded('product', getFilePath($this->product->thumbnail_img)),
            'shop_info' => $this->whenLoaded('seller', [
                'seller_name' => $this->seller->shop->name,
                'shop_name' => $this->seller->shop->shop_name,
                'phone' => $this->seller->shop->phone,
                'email' => $this->seller->shop->email,
            ]),
            'status' => $this->status == 2 ? 'Canceled' : ($this->status == 1 ? 'Approved' : 'Pending'),
            'seller' => $sellerSlug,
        ];
    }
}
