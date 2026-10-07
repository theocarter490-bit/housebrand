<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sellerSlug = null;
        if ($this->user->role_id == 3) {
            $sellerSlug = optional($this->user->shop)->slug;
        }
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => (
            $this->variant_min_discount_price !== null && $this->variant_max_discount_price !== null
                ? ($this->variant_min_discount_price == $this->variant_max_discount_price
                ? getPriceFormat($this->variant_min_discount_price)
                : getPriceFormat($this->variant_min_discount_price) . '-' . getPriceFormat($this->variant_max_discount_price)
            )
                : getPriceFormat($this->unit_price)
            ),
            'discount_price' => ($this->variant_min_discount_price !== null && $this->variant_max_discount_price !== null) || $this->discount_price == 0
                ? null
                : getPriceFormat($this->discount_price),
            'discountPercentage' => $this->discount_percentage,
            'price_hidden' => (bool)$this->is_price_hidden,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'name' => $this->category->name,
                ];
            }),
            'image' => asset(getFilePath($this->thumbnail_img)),
            'images' => $this->whenLoaded('images', function () {
                $paths = $this->images->pluck('path')->toArray();
                array_unshift($paths, $this->thumbnail_img); // Add thumbnail to start
                return array_map(fn($path) => asset(getFilePath($path)), array_unique($paths));
            }),
            'wishlist' => (bool)$this->whenCounted('wishlist'),
            'seller' => $sellerSlug,
            'x_value' => $this->when($this->pivot?->x !== null, fn() => $this->pivot->x),
            'y_value' => $this->when($this->pivot?->y !== null, fn() => $this->pivot->y),
        ];
    }
}
