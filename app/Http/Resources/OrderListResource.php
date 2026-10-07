<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'order_code'    => $this->code,
            'shop_name'     => $this->whenLoaded('shop', @$this->shop->shop_name),
            'item_count'    => $this->whenCounted('items'),
            'amount'        => $this->grand_total_amount,
            'date_time'     => dateFormatwithTime($this->order_date),
            'payment_status'=> $this->payment_status,
            'status'        => $this->whenLoaded('orderStatus', new StatusResource($this->orderStatus)),
        ];
    }
}
