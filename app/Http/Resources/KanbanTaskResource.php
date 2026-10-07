<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KanbanTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->name,
            "title" => $this->name,
            "color" => $this->color,
            "item" => [
                [
                    "id" => "in-progress-1",
                    "title" => "Research FAQ page UX",
                    "comments" => "12",
                    "badge-text" => "UX",
                    "badge" => "success",
                    "due-date" => "5 April",
                    "attachments" => "4",
                    "assigned" => [
                        "12.png",
                        "5.png"
                    ],
                    "members" => [
                        "Bruce",
                        "Clark"
                    ]
                ],
                [
                    "id" => "in-progress-2",
                    "title" => "Review Javascript code",
                    "comments" => "8",
                    "badge-text" => "Code Review",
                    "badge" => "danger",
                    "attachments" => "2",
                    "due-date" => "10 April",
                    "assigned" => [
                        "3.png",
                        "8.png"
                    ],
                    "members" => [
                        "Helena",
                        "Iris"
                    ]
                ]
            ]
        ];
    }
}
