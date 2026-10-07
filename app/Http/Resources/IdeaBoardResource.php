<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IdeaBoardResource extends JsonResource
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
            'title' => $this->title,
            'project_title' => $this->project->title,
            'code' => $this->code,
            'image' => getFilePath($this->image),
            'budget' => getPriceFormat($this->budget),
            'expense' => getPriceFormat($this->items_sum_price),
            'description' => $this->budget,
            'progress' => round(($this->items_sum_price / $this->budget) * 100),
            'active_status' => $this->active_status == 1 ? "Active" : "Inactive",
            'items' =>IdeaBoardItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
