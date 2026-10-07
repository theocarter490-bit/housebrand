<?php

namespace App\Http\Resources;

use App\Models\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'request_id' => $this->id,
            'product' => new ProductListResource($this->whenLoaded('product')),
            'variation' => $this->variation,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'status' => match ($this->getRawOriginal('status')) {
                ProductRequest::APPROVED => 'Approved',
                ProductRequest::CANCELED => 'Canceled',
                ProductRequest::COMPLETED => 'Completed',
                ProductRequest::ADDED_TO_CART => 'Approved',
                default => 'Pending',
            }];
    }
}
