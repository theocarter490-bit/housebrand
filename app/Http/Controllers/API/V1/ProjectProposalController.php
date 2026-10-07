<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectProposalResource;
use App\Models\ProjectProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        $query = ProjectProposal::with('project')
            ->whereHas('project', function ($q) {
                $q->where('client_id', Auth::guard('sanctum')->user()->id);
            })->where('project_id', $request->project_id)
            ->where('is_published', 1);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $projectProposal = $query->latest()->paginate(perPage());

        return sendResponse('Project Proposal List',
            ProjectProposalResource::collection($projectProposal)->resource);
    }

    public function details($projectProposalId)
    {
        $projectProposal = ProjectProposal::where('id', $projectProposalId)->with(['items' => function ($query) {
            $query->with('service', 'product');
        }, 'invoice'])->first();

        return sendResponse('Proposal details', new ProjectProposalResource($projectProposal));
    }

    public function approveStatus($projectProposalId)
    {
        $projectProposal = ProjectProposal::where('id', $projectProposalId)->first();
        $projectProposal->is_approved = 1;
        $projectProposal->save();

        return sendResponse('Project Proposal Approved');
    }

    public function rejectStatus($projectProposalId)
    {
        $projectProposal = ProjectProposal::where('id', $projectProposalId)->first();
        $projectProposal->is_approved = 2;
        $projectProposal->save();

        return sendResponse('Project Proposal Rejected');
    }

}
