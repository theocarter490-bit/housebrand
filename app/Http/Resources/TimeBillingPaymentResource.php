<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeBillingPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->payment_date,
            'amount' => $this->amount,
            'due' => $this->current_due,
            'note' => $this->note,
            'method' => $this->whenLoaded('paymentMethod', $this->paymentMethod->name),
            'logo' => $this->whenLoaded('paymentMethod', getFilePath($this->paymentMethod->logo)),
        ];
    }
}
