<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductChoiceOptionResource extends JsonResource
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
            'name' => $this->name,
            'attributes' => $this->whenLoaded('values', function () {
                $arr = [];
                foreach ($this->values as $value) {
                    if (in_array($value->name, $this->pivot->value)) {
                        $arr[] =  [
                            'id' => $value->id,
                            'name' => $value->name,
                            'value' => $value->value,
                        ];
                    }
                }
                return $arr;
            }),
        ];
    }
}
