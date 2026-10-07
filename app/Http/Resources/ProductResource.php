<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'thumbnail_link' => asset(getFilePath($this->thumbnail_img)),
            'video_link' => $this->video_link,
            'tags' => json_decode($this->tags),
            'description' => $this->description,
            'price' => $this->unit_price,
            'discount_price' => $this->discount_price,
            'discountPercentage' => $this->discount_percentage,
            'price_hidden' => (bool)$this->is_price_hidden,
            'unit' => $this->unit,
            'shipping_policy' => $this->shipping_policy,
            'return_policy' => $this->return_policy,
            'disclaimer' => $this->disclaimer,
            'category' => new CategoryResource($this->category),
            'brand' => new BrandResource($this->brand),
            'variants' => ProductVariantResource::collection($this->variants),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'weight_dimensions' => $this->weight_dimensions,
            'specifications' => $this->specifications,
            'options' => ProductChoiceOptionResource::collection($this->choiceOptions),
            'wishlist' => (bool)$this->wishlist_count,
            'sharedProduct' => (bool)$this->shared_product_count,
            'seller_id' => $this->user_id,
            'shop' => new ShopResource($this->shop),
            'average_rating' => getProductAverageRating($this->resource),
            'total_reviews' => getProductTotalReviews($this->resource),
            'meta_ifno' => [
                'title' => @$this->meta_title,
                'description' => @$this->meta_description,
                'image' => asset(getFilePath($this->meta_img)),
            ],
        ];
    }
}
