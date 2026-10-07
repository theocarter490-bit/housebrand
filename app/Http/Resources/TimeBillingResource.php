<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeBillingResource extends JsonResource
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
            'code' => $this->code,
            'bill_type' => $this->bill_type_name,
            'rate' => $this->rate,
            'duration' => secondsToHMS($this->duration),
            'total_amount' => $this->total_amount,
            'payment_status' => match ($this->payment_status) {
                1 => 'Paid',
                2 => 'Partial',
                default => 'Unpaid',
            },
            'description' => $this->description,
            'payment_detials' => TimeBillingPaymentResource::collection($this->whenLoaded('paymentDetails')) ,
            'service' => $this->whenLoaded('serviceType', function () {
                return @$this->serviceType->projectService->title;
            })
        ];
    }
}
