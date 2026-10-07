<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = [];
        $ourMission = [];
        if ($this->slug == 'about') {
            if (count($this->sliders) > 0) {
                foreach ($this->sliders as $item) {
                    $items[] = [
                        'title' => $item->title,
                        'description' => $item->description,
                        'image' => getFilePath($item->image),
                    ];
                }
            }
            $ourMission = ['our_mission' => $this->our_mission];
        }
        if ($this->slug == 'team') {
            if (count($this->items) > 0) {
                foreach ($this->items as $item) {
                    $items[] = [
                        'name' => $item->name,
                        'designation' => $item->description,
                        'image' => getFilePath($item->image),
                    ];
                }
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'image' => getFilePath($this->image),
            'items' => $items
        ]+ $ourMission;
    }
}
