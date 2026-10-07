<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectProposalInvoiceListResource extends JsonResource
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
            'title' => $this->title,
            'code' => $this->code,
            'invoice_date' => dateFormat($this->invoice_date),
            'sub_total' => getPriceFormat($this->sub_total),
            'tax_amount' => getPriceFormat($this->tax_amount),
            'discount_amount' => getPriceFormat($this->discount_amount),
            'shipping_charge' => getPriceFormat($this->shipping_charge),
            'deposit_amount' => getPriceFormat($this->deposit_amount),
            'total_amount' => getPriceFormat($this->total_amount),
            'payment_status' => match ($this->payment_status) {
                0 => "Unpaid",
                1 => "Paid",
                2 => "Partially Paid",
            },
            'active_status' => $this->active_status == 1 ? 'Active' : 'Inactive',
            'signature' => getFilePath($this->signature),
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->id,
                    'title' => $this->project->title,
                    'code' => $this->project->project_code
                ];
            }),
            'items' => ProjectProposalItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
