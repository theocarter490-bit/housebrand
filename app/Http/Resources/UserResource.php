<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'phone' => optional($this->shop)->phone,
            'email' => optional($this->shop)->email,
            'address' => optional($this->shop)->location,
            'avatar' => asset(getFilePath($this->avatar)),
            'status' => $this->active_status,
            'shop_name' => optional($this->shop)->shop_name,
            'shop_slug' => optional($this->shop)->slug,
            'logo' => asset(getFilePath(optional($this->shop)->logo)),
            'banner' => asset(getFilePath(optional($this->shop)->banner)),
            'total_product' => $this->products_count,
            'total_portfolio' => $this->portfolio_count,
            'total_inspiration' => $this->inspiration_count,
            'average_rating' => getAverageRating($this),
            'total_review' => $this->whenCounted('reviews'),
            'reviews' => DesignerReviewResource::collection($this->whenLoaded('reviews')),
            'social_links' => $this->whenLoaded('shop', function () {
                return [
                    'instagram_url' => $this->shop->instagram_url,
                    'tiktok_url' => $this->shop->tiktok_url,
                    'facebook_url' => $this->shop->facebook_url,
                    'youtube_url' => $this->shop->youtube_url,
                    'linkedin_url' => $this->shop->linkedin_url,
                    'twitter_url' => $this->shop->twitter_url,
                ];
            }),
        ];
    }
}
