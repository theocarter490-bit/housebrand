<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Project;
use App\Models\SpecialSection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SpecialSectionListResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch projects
        $projects = Project::where('client_id', Auth::guard('sanctum')->user()->id)
            ->where('active_status', 1)
            ->with(['status', 'category'])
            ->get();

        // Fetch inspirations
        $inspirations = SpecialSection::where('type', 2)
            ->where('user_id', Auth::guard('sanctum')->user()->designer_id)
            ->where('is_active', 1)
            ->take(3)
            ->get();

        // Return both in response
        return sendResponse('Project List', [
            'projects'      => ProjectResource::collection($projects)->resource,
            'inspirations'  => SpecialSectionListResource::collection($inspirations)->resource,
        ]);
    }


    public function details($project_id)
    {
        $project = Project::where('client_id', Auth::guard('sanctum')->user()->id)->where('id', $project_id)->where('active_status', 1)->with('status', 'category')->first();
        if (!$project) {
            return sendError('Project not found');
        }
        return sendResponse('Project Details', new ProjectResource($project));
    }
}
