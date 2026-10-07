<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderClaimResource extends JsonResource
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
            'user' => $this->user->name,
            'order_id' => $this->order_id,
            'order_code' => $this->order->code,
            'subject'       => $this->subject,
            'order_claim_issue_type' => $this->orderClaimIssueType->name,
            'details'       => $this->details,
            'status'        => $this->status,
            'file'          => $this->file ? asset(getFilePath($this->file)): null,
            'date_time'     => $this->date_time,
            'created_by'    => $this->createdBy->name
        ];
    }
}
