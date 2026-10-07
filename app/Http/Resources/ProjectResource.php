<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'project_code' => $this->project_code,
            'banner' => getFilePath($this->banner),
            'description' => $this->description,
            'start_date' => isset($this->start_date) ? dateFormat($this->start_date) : 'N/A',
            'end_date' => isset($this->end_date) ? dateFormat($this->end_date) : 'N/A',
            'project_status' => $this->status?->name,
            'project_category' => $this->category->name,
            'priority' => $this->priority,
            'budget' => getPriceFormat($this->budget),
        ];
    }
}
