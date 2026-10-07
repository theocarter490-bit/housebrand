<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
            'order_code' => $this->code,
            'amount' => $this->grand_total_amount,
            'date_time' => dateFormatwithTime($this->order_date),
            'order_status' => $this->whenLoaded('orderStatus', new StatusResource($this->orderStatus)),
            'payment_status' => $this->payment_status,
            'name' => optional($this->shipping_address)->name,
            'email' => optional($this->shipping_address)->email,
            'phone' => optional($this->shipping_address)->phone,
            'street' => optional($this->shipping_address)->street_address,
            'state' => optional($this->shipping_address)->state,
            'zip_code' => optional($this->shipping_address)->zip_code,
            'country' => optional($this->shipping_address)->country,
            'shipping_charges' => $this->shipping_charges,
            'sub_total_amount' => $this->sub_total_amount,
            'admin_discount_amount' => $this->admin_discount_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total_amount' => $this->grand_total_amount,
            'paid_amount' => $this->paid_amount,
            'note' => $this->note,
            'order_items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment_details' => PaymentHistoryResource::collection($this->whenLoaded('paymentDetails')),
        ];
    }
}
