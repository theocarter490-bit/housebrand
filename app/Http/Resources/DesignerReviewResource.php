<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignerReviewResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' =>[
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'role' => $this->customer->role->name,
                'avatar' => getFilePath($this->customer->avatar),
            ],
            'review' =>[
                'id' => $this->id,
                'review_type' => $this->reviewType->name,
                'review' => $this->review,
                'rating' => $this->rating,
                'status' => $this->active_status,
                'date' => dateFormat($this->created_at),
            ],
        ];
    }
}
