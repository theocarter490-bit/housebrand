<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'author'        => $this->whenLoaded('createdBy', function () {
                return [
                    'name'  => $this->createdBy->name,
              ];
            }),
            'title'         => $this->title,
            'published_at'  => dateFormatwithTime($this->publish_at),
            'slug'          => $this->slug,
            'short_desc'    => $this->desc,
            'category'      => $this->whenLoaded('category', function () {
                return [
                    'name'  => $this->category->name,
                ];
            }),
            'thumbnail'     => getFilePath($this->thumbnail),
            'banner'        => getFilePath($this->banner),
            'tags'          => json_decode($this->tags),
            'view_count'    => $this->view_count,
            'serial_no'     => $this->serial_no
        ];
    }
}
