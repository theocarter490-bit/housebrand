<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
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
            'name' => $this->whenLoaded('paymentMethod',$this->paymentMethod->name),
            'logo' => $this->whenLoaded('paymentMethod',asset($this->paymentMethod->logo)),
            'type' => $this->whenLoaded('paymentMethod',function (){
                return match($this->paymentMethod->id){
                    1 => 'stripe',
                    2 => 'paypal',
                    default => 'cash',
                };
            })
        ];
    }
}
