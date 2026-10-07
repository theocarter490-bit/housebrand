<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimRepliesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'user'      => $this->user->name,
            'avatar'    => $this->whenLoaded('user',getFilePath($this->user->avatar)),
            'details'   => $this->details,
            'date_time' => dateFormatwithTime($this->created_at),
            'file'      => $this->file ? getFilePath($this->file): null,
        ];
    }
}
