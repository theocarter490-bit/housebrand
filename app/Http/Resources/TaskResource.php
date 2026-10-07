<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assignes = [];
        $members = [];
        foreach ($this->assignedUsers ?? [] as $user) {
            $assignes[] = getFilePath($user['avatar']);
            $members[] = $user['name'];
        }
        return [
            "id" => (string)$this->id,
            "title" => $this->title,
            "descripiton" => $this->description,
            "image" => $this->when(@$this->taskThumb, getFilePath(@$this->taskThumb->file)),
            "comments" => $this->taskComments->count(),
            "badge-text" => $this->taskLabel->name,
            "badge" => $this->taskLabel->color,
            "due-date" => $this->due_date,
            "attachments" => $this->task_files_count,
            "assigned" => $assignes,
            "members" => $members,
        ];
    }
}
