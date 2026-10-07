<?php

namespace App\Http\Resources;

use App\Models\CustomerAssignedDesignerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableDesignerListResource extends JsonResource
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
            'designer' => new ShopResource($this->shop),
            'request_id' => $this->when(
                $this->relationLoaded('designerJoinRequest') && $this->designerJoinRequest,
                fn() => $this->designerJoinRequest->id
            ),
            'request_status' => $this->when($this->relationLoaded('designerJoinRequest') && $this->designerJoinRequest, function () {
                return match ($this->designerJoinRequest->getRawOriginal('status')) {
                    CustomerAssignedDesignerRequest::APPROVED => 'Approved',
                    CustomerAssignedDesignerRequest::DECLINED => 'Declined',
                    CustomerAssignedDesignerRequest::CANCELED => 'Canceled',
                    default => 'Waiting For Approval',
                };
            }),

            'customer_note' => $this->when(
                $this->relationLoaded('designerJoinRequest') && $this->designerJoinRequest,
                fn() => $this->designerJoinRequest->customer_note
            ),

            'admin_note' => $this->when(
                $this->relationLoaded('designerJoinRequest') && $this->designerJoinRequest,
                fn() => $this->designerJoinRequest->admin_note
            ),
        ];
    }
}
