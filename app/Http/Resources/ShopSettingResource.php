<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopSettingResource extends JsonResource
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
            'logo' => getFilePath($this->logo),
            'favicon' => getFilePath($this->favicon),
            'loader' => getFilePath($this->loader),
            'banner' => getFilePath($this->banner),
            'location' => $this->location,
            'map_location' => $this->map_location,
            'phone' => $this->phone,
            'email' => $this->email,
            'home_slider_style' => $this->home_slider_style,
            'emergency_notice' => $this->emergency_notice,
            'emergency_notice_status' => $this->emergency_notice_status == 1 ? true : false,
            'shop_status' => $this->shop_status == 1 ? true : false,
            'social_links' => [
                'instagram_url' => $this->instagram_url,
                'tiktok_url' => $this->tiktok_url,
                'facebook_url' => $this->facebook_url,
                'youtube_url' => $this->youtube_url,
                'linkedin_url' => $this->linkedin,
                'twitter_url' => $this->twitter_url,
            ],
            'section_content' => json_decode($this->section_content),
            'subscription_status' => isSubscribed($this->seller)
        ];
    }
}
