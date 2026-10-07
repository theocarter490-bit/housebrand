<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectProposalResource extends JsonResource
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
            'proposal_date' => dateFormat($this->proposal_date),
            'due_date' => dateFormat($this->due_date),
            'sub_total' => getPriceFormat($this->sub_total),
            'tax_amount' => getPriceFormat($this->tax_amount),
            'discount_amount' => getPriceFormat($this->discount_amount),
            'shipping_charge' => getPriceFormat($this->shipping_charge),
            'deposit_amount' => getPriceFormat($this->deposit_amount),
            'total_amount' => getPriceFormat($this->total_amount),
            'active_status' => $this->active_status == 1 ? 'Active' : 'Inactive',
            'approved_status' => match ($this->is_approved) {
                0 => "Pending",
                1 => "Approved",
                2 => "Rejected",
            },
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->id,
                    'title' => $this->project->title,
                    'code' => $this->project->project_code
                ];
            }),
            'invoice' => !$this->invoice ?
                [
                    'has_invoice' => false,
                    'link' => null,
                ] :

                [
                    'has_invoice' => true,
                    'invoice_id' => $this->invoice->id,
                ]
            ,
            'items' => ProjectProposalItemResource::collection($this->whenLoaded('items')),];
    }


    private
    function getTaxTypeLabel($taxType): string
    {
        return match ($taxType) {
            0 => 'No Tax',
            1 => 'Percentage',
            2 => 'Fixed',
            default => 'Unknown',
        };
    }
}
