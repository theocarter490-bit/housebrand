<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Project;
use App\Models\ProposalItem;
use App\Models\ProjectProposal;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\ProjectProposalItemRequest;

class ProjectProposalItemController extends Controller
{
    use FileUploadTrait;

    public function store(ProjectProposalItemRequest $request, $projectId, $proposalId)
    {
        try {
            DB::beginTransaction();

            $proposal = ProjectProposal::find($proposalId);
            $proposal->sub_total = $request->sub_total;
            $proposal->tax_type = $request->tax_type;
            $proposal->tax_value = $request->tax_value;
            $proposal->tax_amount = $request->tax_amount;
            $proposal->discount_type = $request->discount_type;
            $proposal->discount_value = $request->discount_value;
            $proposal->discount_amount = $request->discount_amount;
            $proposal->shipping_charge = $request->shipping_charge;
            $proposal->deposit_amount = $request->deposite_request;
            $proposal->total_amount = $request->total;

            if ($request->hasFile('signature')) {
                $path = $this->uploadFile($request->file('signature'), 'project/' . $projectId . '/proposals');
                $proposal->signature = $path;
            }

            $proposal->save();

            $proposalItem = ProposalItem::where('project_proposal_id', $proposalId)->delete();

            if ($request->product) {
                foreach ($request->product as $item) {
                    $proposalItem = new ProposalItem();

                    $proposalItem->project_proposal_id = $proposal->id;
                    $proposalItem->product_id = $item['product_id'];
                    $proposalItem->unit_price = $item['price'];
                    $proposalItem->quantity = $item['quantity'];
                    $proposalItem->type = $item['type'];
                    $markupPercent = $item['markup'] ?? 0;
                    $proposalItem->markup = $markupPercent;

                    $markupAmount = $item['price'] * ($markupPercent / 100);
                    $proposalItem->price = ($item['price'] + $markupAmount) * $item['quantity'];

                    $proposalItem->save();

                    $variant_value = [];
                    if (array_key_exists('variant', $item) && sizeof($item['variant'])) {
                        foreach ($item['variant'] as $variant) {
                            $value = [
                                'attribute' => $variant['attribute'],
                                'value' => $variant['value'],
                            ];
                            array_push($variant_value, $value);
                        }
                    }

                    $proposalItem->variation = $variant_value;


                    $proposalItem->save();
                }
            }
            DB::commit();
            $project = Project::find($projectId);
            Toastr::success('Proposal Item Update Successfully');
            return redirect()->route('project-management.project.proposal.index', $proposal->project_id)->with('project', $project);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            return abort(404, "Something went wrong");
        }
    }
}
