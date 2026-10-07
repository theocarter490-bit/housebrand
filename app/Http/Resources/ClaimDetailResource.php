<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ClaimRepliesResource;

class ClaimDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'claim_info' => [
                'id' => $this->id,
                'user' => $this->user->name,
                'order_claim_issue_type' => $this->orderClaimIssueType->name,
                'subject' => $this->subject,
                'details' => $this->details,
                'status' => $this->status,
                'file' => $this->file ? asset(getFilePath($this->file)) : null,
                'date_time' => dateFormatwithTime($this->date_time),
            ],

            'order_info' => [
                'id' => $this->order->id,
                'code' => $this->order->code,
                'status' => $this->order->orderStatus->name,
                'date_time' => $this->order->order_date,
                'payment_status' => $this->order->payment_status,
                'total_amount' => $this->order->grand_total_amount,
            ],
            'shop_info' => [
                'id' => $this->order->shop->id,
                'name' => $this->order->shop->shop_name,
                'logo' => getFilePath($this->order->shop->logo),
            ],


            'replies' => ClaimRepliesResource::collection($this->replies),
        ];
    }
}
