<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from_id' => $this->from_id,
            'to_id' => $this->to_id,
            'message' => $this->message??null,
            'file' => $this->file ? getFilePath($this->file) : null,
            'created_at' => Carbon::parse($this->created_at)->format('M j, g:i A'),
        ];
    }
}
