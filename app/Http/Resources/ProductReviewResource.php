<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->user->role->name,
                'avatar' => getFilePath($this->user->avatar),
            ],
            'review' => [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'review_type' => $this->reviewType->name,
                'review' => $this->review,
                'rating' => $this->rating,
                'status' => $this->active_status,
                'date' => dateFormat($this->created_at),
            ],
            
        ];
    }
}
