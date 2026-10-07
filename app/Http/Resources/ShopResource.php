<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shop_name' => $this->shop_name,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo' => getFilePath($this->logo),
            'banner' => getFilePath($this->banner),
            'average_rating' => getAverageRating($this->seller),
            'products' => $this->seller->products->count(),
            'portfolio' => $this->seller->portfolio->count(),
            'inspiration' => $this->seller->inspiration->count(),
            'total_review' => $this->seller->reviews->count(),
            'subscription_status' => isSubscribed($this->seller)
        ];
    }
}
