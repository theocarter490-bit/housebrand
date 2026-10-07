<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeBillingDetailResource extends JsonResource
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
            'date' => dateFormatwithTime($this->created_at),
            'bill_type' => $this->bill_type_name,
            'payment_status' => match ($this->payment_status) {
                1 => 'Paid',
                2 => 'Partial',
                default => 'Unpaid',
            },
            'client' =>[
                'name' => $this->client->name,
                'email' => $this->client->email,
                'phone' => $this->client->phone,
                'address' => $this->client->address,
            ],
            'items' =>[
                'service' => @$this->serviceType->projectService->name,
                'rate' => getPriceFormat($this->rate),
                'duration' => secondsToHMS($this->duration),
                'total_amount' => getPriceFormat($this->total_amount),
                'paid_amount' => getPriceFormat($this->paid_amount ?? 0),
                'due_amount' => getPriceFormat($this->total_amount - ($this->paid_amount ?? 0)),
            ],
        ];
    }
}
