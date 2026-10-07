<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'colors' => [
                'primary'       => $this->primary,
                'secondary'     => $this->secondary,
                'bg_primary'    => $this->bg_primary,
                'bg_secondary'  => $this->bg_secondary,
                'text_primary'  => $this->text_primary,
                'text_secondary'=> $this->text_secondary,
            ],
        ];
    }

}
