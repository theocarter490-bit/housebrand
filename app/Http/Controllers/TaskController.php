<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskAddAttachmentRequest;
use App\Http\Requests\TaskAssignUserRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Models\Task;
use App\Models\Project;
use App\Models\TaskComment;
use App\Models\TaskFile;
use App\Models\TaskChecklist;
use App\Models\TaskLabel;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TaskActivityLog;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\KanbanTaskResource;
use App\Http\Resources\TaskStatusResource;

class TaskController extends Controller
{

    use FileUploadTrait;


    public function index(Request $request)
    {
        $projects = Project::with('category')->isClient('user_id');

        // Apply search filter if the request has 'search'
        if ($request->filled('search')) {
            $projects->where('title', 'LIKE', '%' . $request->search . '%');
        }
        $projects = $projects->paginate(9);
        return view('project-management.project.projects.task.index', compact('projects'));
    }

    public function taskStatusStore(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|required',
                'color' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $message) {
                    Toastr::error($message, 'Error');
                }
                return redirect()->back();
            }
            $count = TaskStatus::count();
            $taskStatus = new TaskStatus();
            $taskStatus->name = $request->name;
            $taskStatus->color = $request->color;
            $taskStatus->project_id = $id;
            $taskStatus->serial_number = $count + 1;
            $taskStatus->save();


            Toastr::success('Task status created successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!');
            return redirect()->back();
        }
    }

    public function kanban($project_id)
    {
        $taskStatus = TaskStatus::with(['tasks' => function ($q) use ($project_id) {
            $q->where('project_id', $project_id)->with('taskLabel', 'assignedUsers')->withCount('taskFiles');
        }])->where(function ($query) use ($project_id) {
            $query->where('project_id', $project_id)
                ->orWhere(function ($q) {
                    $q->whereNull('project_id')->where('system_default', 1);
                });
        })->where('active_status', 1)->orderBy('serial_number')->get();
        return sendResponse('All products list.', TaskStatusResource::collection($taskStatus)->resource);
    }

    public function getTask($task_id)
    {
        $task = Task::with('taskLabel')->where('id', $task_id)->first();
        return response()->json($task);
    }

    public function store(TaskStoreRequest $request, $project_id)
    {
        try {
            DB::beginTransaction();
            $task = new Task();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->priority = $request->priority;
            $task->task_status_id = $request->create_task_status_id;
            $task->task_label_id = $request->label;
            $task->project_id = $project_id;
            $task->due_date = $request->due_date;
            $task->assigned_users = [];
            $task->save();

            if ($request->hasFile('attachment')) {
                $taskFile = new TaskFile();
                $path = $this->uploadFile($request->file('attachment'), 'projects/' . $project_id . '/tasks');
                $taskFile->file = $path;
                $taskFile->user_id = auth()->id();
                $taskFile->task_id = $task->id;
                $taskFile->save();
            }

            $task->save();

            createTaskActivityLog($task->id, 'created this task');

            DB::commit();
            Toastr::success('Task created successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong!');
        }
    }

    public function update(Request $request, $project_id)
    {

        try {
            DB::beginTransaction();
            $task = Task::where('id', $request->task_id)->where('project_id', $project_id)->first();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->priority = $request->priority;
            $task->task_label_id = $request->label;
            $task->due_date = $request->due_date;

            if ($request->hasFile('attachment')) {
                $taskFile = new TaskFile();
                $path = $this->uploadFile($request->file('attachment'), 'projects/' . $project_id . '/tasks');
                $taskFile->file = $path;
                $taskFile->user_id = auth()->id();
                $taskFile->task_id = $task->id;
                $taskFile->save();
            }

            $task->save();
            createTaskActivityLog($task->id, 'updated this task');
            DB::commit();
            Toastr::success('Task updated successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong!');
        }
    }

    public function details($task_id)
    {
        $task = Task::with('project', 'taskLabel', 'status', 'taskFiles', 'activityLogs.user', 'assignedUsers')
            ->where('id', $task_id)->first();

        $taskStatus = TaskStatus::where('active_status', 1)
            ->where('project_id', $task->project->id)
            ->get();


        $project = $task->project;
        hasPermissionForOperation($project, 'user_id');
        $taskChecklist = TaskChecklist::where('task_id', $task_id)
            ->where('user_id', \Auth::user()->id)->latest()->get();

        // Calculate total and completed checklists
        $totalChecklists = $taskChecklist->count();
        $completedChecklists = $taskChecklist->where('active_status', 1)->count();

        // Calculate completion percentage
        $completionPercentage = $totalChecklists > 0 ? ($completedChecklists / $totalChecklists) * 100 : 0;

        $completionPercentage = round($completionPercentage);

        $assignedUsers = $task->assignedUsers;

        $assignedUsersJson = $task->assignedUsers->map(function ($user) {
            return [
                'value' => $user->id,
                'name' => $user->name,
                'avatar' => getFilePath($user->avatar),
            ];
        })->toJson();

        $taskComments = TaskComment::with('user')
            ->where('task_id', $task_id)
            ->latest()->get();

        return view('project-management.project.projects.task.details', compact('project',
            'task', 'taskChecklist', 'assignedUsers', 'assignedUsersJson',
            'taskStatus', 'taskComments', 'completionPercentage'));
    }

    public function changeStatus(Request $request, $project_id)
    {
        $task = Task::where('project_id', $project_id)->where('id', $request->task_id)->update(
            [
                'task_status_id' => $request->status_id,
            ]
        );
        createTaskActivityLog($request->task_id, 'move this task to ' . TaskStatus::find($request->status_id)->name);
        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Request $request, $project_id)
    {
        try {
            Task::where('id', $request->task_id)->where('project_id', $project_id)->delete();
            createTaskActivityLog($request->task_id, 'deleted this task');
            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong!"], 500);
        }
    }

    public function taskStatusCounts($project_id)
    {
        $taskStatusCounts = TaskStatus::where(function ($query) use ($project_id) {
            $query->where('project_id', $project_id)
                ->orWhere(function ($q) {
                    $q->whereNull('project_id')->where('system_default', 1);
                });
        })
            ->withCount(['tasks' => function ($q) use ($project_id) {
                $q->where('project_id', $project_id);
            }])->get();

        return response()->json(['data' => $taskStatusCounts]);
    }

    public function addAttachment(TaskAddAttachmentRequest $request)
    {
        try {
            if ($request->hasFile('file')) {
                $taskFile = new TaskFile();
                $path = $this->uploadFile($request->file('file'), 'task/' . $request->projectId . '/tasks');
                $taskFile->file = $path;
                $taskFile->user_id = auth()->id();
                $taskFile->task_id = $request->taskID;
                $taskFile->save();
                createTaskActivityLog($request->taskID, 'attached new file');
            }
            Toastr::success('Attachment added successfully');
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }

    public function deleteAttachment(Request $request)
    {
        try {
            $data = TaskFile::findOrFail($request->id);
            $data->delete();
            return response()->json(['message' => 'Attachment deleted successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!' . $e->getMessage(), 'status' => 500]);
        }

    }

    public function getUsers()
    {
        $authUserId = getUserId();
        $users = User::where('supervisor_id', $authUserId)->get();
        $users = $users->map(function ($user) {
            $user->avatar = getFilepath($user->avatar);
            return $user;
        });
        return response()->json($users);
    }

    public function assignUser(TaskAssignUserRequest $request)
    {
        try {
            $tagifyUsers = json_decode($request->TagifyUserList, true);
            $userIds = array_map(fn($user) => (int)$user['value'], $tagifyUsers);
            $task = Task::findOrFail($request->taskID);
            $task->assigned_users = $userIds;
            $task->save();
            Toastr::success('User assign successfully');

            foreach ($tagifyUsers as $user) {
                createTaskActivityLog($task->id, 'assigned a user ' . $user['name']);
            }
            createTaskActivityLog($task->id, 'assigned a user');
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }
}
