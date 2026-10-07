<?php

namespace App\Http\Resources;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostDetailsResource extends JsonResource
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
            'title'         => $this->title,
            'author'        => $this->whenLoaded('createdBy', function () {
                return [
                    'name'  => $this->createdBy->name,
                ];
            }),
            'category'      => $this->whenLoaded('category', function () {
                return [
                    'name'  => $this->category->name,
                ];
            }),
            'created_at'    => dateFormatwithTime($this->created_at),
            'publish_at'    => dateFormatwithTime($this->publish_at),
            'description'   => $this->desc,
            'tags'          => json_decode($this->tags),
            'video_url'     => getEmbedUrl($this->video_url),
            'banner'        => $this->banner ? getFilePath($this->banner):null,
            'content_details' => $this->contentDetails->map(function($detail) {
                return [
                    'section_type' => $detail->section_type,
                    'serial' => $detail->serial,
                    'items' => $detail->items->map(function($item) {
                        return [
                            'description' => $item->description,
                            'image' => getFilePath($item->image),
                        ];
                    }),
                ];
            }),

            'related_posts' => BlogPostResource::collection(BlogPost::with("createdBy")->where('category_id', $this->category_id)->where('id', '!=', $this->id)->limit(6)->get()),
        ];
    }
}
