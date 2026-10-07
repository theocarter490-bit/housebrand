<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\IdeaBoard;
use App\Models\ProjectProposal;
use App\Models\ProjectProposalInvoice;
use App\Models\Role;
use App\Models\Task;
use App\Models\TimeBilling;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskLabel;
use App\Models\TaskStatus;
use Google\Service\AdExchangeBuyer\Proposal;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\ProjectCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;

class ProjectController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        $projects = Project::with(['category', 'manager', 'client'])->latest()->isClient();

        if ($request->filled('search')) {
            $projects->where('title', 'LIKE', '%' . $request->get('search') . '%');
        }

        if ($request->filled('category')) {
            $projects->where('project_category_id', $request->get('category'));
        }

        if ($request->filled('status')) {
            $projects->where('project_status_id', $request->get('status'));
        }

        $projects = $projects->paginate(perPage())->through(function ($project) {
            $project->progressValue = getProjectProgressValue($project);
            return $project;
        });


        $projectStatus = ProjectStatus::where('active_status', 1)->get();
        $projectCategories = ProjectCategory::where('active_status', 1)->get();

        return view('project-management.project.projects.index', compact('projects', 'projectStatus', 'projectCategories'));
    }

    public function overview($id)
    {
        $project = Project::with([
            'category',
            'tasks',
            'status',
            'manager',
            'client',
            'timeBillings' => function ($query) {
                $query->latest()->limit(5); // Only 5 records, ordered by latest
            },
            'invoices' => function ($query) {
                $query->latest()->limit(5); // Only 5 records, ordered by latest
            }
        ])
            ->where('id', $id)
            ->isClient()
            ->first();

        hasPermissionForOperation($project, 'user_id');
        $taskStatus = TaskStatus::with(['tasks' => function ($q) use ($project) {
            $q->where('project_id', $project->id)
                ->orderBy('created_at', 'desc');
        }])
            ->whereHas('tasks', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->where('active_status', 1)
            ->where(function ($query) use ($project) {
                $query->where('project_id', $project->id)
                    ->orWhere(function ($q) {
                        $q->whereNull('project_id')->where('system_default', 1);
                    });
            })
            ->get();
        $totalProgress = getProjectProgressValue($project);

        return view('project-management.project.projects.overview', compact('project', 'taskStatus', 'totalProgress'));
    }

    public function task($id)
    {
        $project = Project::with('category')->where('id', $id)->first();
        hasPermissionForOperation($project, 'user_id');
        $taskLabels = TaskLabel::isClient('user_id')->where('active_status', 1)->get();
        return view('project-management.project.projects.task.kanban', compact('project', 'taskLabels'));

    }

    public function create()
    {
        $categories = ProjectCategory::where('active_status', 1)->get();
        $customers = User::where('designer_id', getUserId())->where('role_id', Role::CUSTOMER)->get();
        $employees = User::where('supervisor_id', getUserId())->get();
        $statuses = ProjectStatus::where('active_status', 1)->get();
        return view('project-management.project.projects.create', compact('categories', 'customers', 'statuses', 'employees'));
    }

    public function store(ProjectStoreRequest $request)
    {
        if (moduleConditionLimitCheck('project-management', 'App\Models\Project') == false) {
            Toastr::error('You have reached the maximum quantity for this module.');
            return redirect()->back();
        }
        try {

            DB::beginTransaction();
            $project = new Project();

            $dateString = $request->date;
            $dates = explode(" to ", $dateString);

            $result = null;
            if ($request->has('tags') && $request->tags != null) {
                $array = json_decode($request->tags, true);
                $result = implode(',', array_column($array, 'value'));
            }
            $project->title = $request->title;
            $project->project_code = "PROJ-" . time();
            $project->description = $request->description;
            $project->start_date = !empty($dates[0]) ? $dates[0] : null;
            $project->end_date = !empty($dates[1]) ? $dates[1] : null;
            $project->user_id = getUserId();
            $project->project_status_id = $request->status;
            $project->client_id = $request->customer;
            $project->project_category_id = $request->category;
            $project->project_manager_id = $request->manager;
            $project->priority = $request->priority;
            $project->budget = $request->budget;
            $project->tax_type = $request->tax_type;
            $project->tax = $request->tax;
            $project->active_status = $request->active_status;
            $project->address = $request->address;
            $project->total_cost = $request->total_cost;
            $project->map_location = $request->map_location;
            $project->tags = $result;
            $project->save();

            if ($request->hasFile('banner')) {
                $path = $this->uploadFile($request->file('banner'), 'project/' . $project->id, null, 400);
                $project->banner = $path;
            }
            $project->save();

            DB::commit();

            Toastr::success('Project Created Successfully!');
            return redirect()->route('project-management.project.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something Went Wrong!');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $project = Project::with('category', 'client', 'status', 'manager')->find($id);

        $categories = ProjectCategory::where('active_status', 1)->get();
        $customers = User::where('designer_id', getUserId())->where('role_id', Role::CUSTOMER)->get();
        $employees = User::where('supervisor_id', getUserId())->get();
        $statuses = ProjectStatus::where('active_status', 1)->get();
        return view('project-management.project.projects.edit', compact('project', 'categories', 'customers', 'statuses', 'employees'));
    }

    public function update(ProjectUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $project = Project::find($id);
            $dateString = $request->date;
            $dates = explode(" to ", $dateString);
            $result = null;
            if ($request->has('tags') && $request->tags != null) {
                $array = json_decode($request->tags, true);
                $result = implode(',', array_column($array, 'value'));
            }
            $project->title = $request->title;
            $project->project_code = "PROJ-" . time();
            $project->description = $request->description;
            $project->start_date = !empty($dates[0]) ? $dates[0] : null;
            $project->end_date = !empty($dates[1]) ? $dates[1] : null;
            $project->project_status_id = $request->status;
            $project->client_id = $request->customer;
            $project->project_category_id = $request->category;
            $project->project_manager_id = $request->manager;
            $project->priority = $request->priority;
            $project->budget = $request->budget;
            $project->tax_type = $request->tax_type;
            $project->tax = $request->tax;
            $project->active_status = $request->active_status;
            $project->address = $request->address;
            $project->total_cost = $request->total_cost;
            $project->map_location = $request->map_location;
            $project->tags = $result;
            $project->save();
            if ($request->hasFile('banner')) {
                $path = $this->uploadFile($request->file('banner'), 'project/' . $project->id);
                $this->deleteFile($project->banner);
                $project->banner = $path;
            }
            $project->save();
            DB::commit();

            Toastr::success('Project updated successfully!');
            return redirect()->route('project-management.project.index');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Toastr::error('Something went wrong!');
            return redirect()->back();
        }
    }
}
