<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => dateFormat($this->payment_date),
            'amount' => $this->amount,
            'due' => $this->current_due,
            'payment_method' => $this->paymentMethod->name,
            'order_code' => $this->order->code,
        ];
    }
}
