<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusStoreRequest;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Models\Project;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskStatusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TaskStatus::select('*')
                ->with(['project' => function ($q) {
                    $q->byShop();
                }])
                ->where(function ($query) {

                    $query->whereHas('project', function ($q) {
                        $q->byShop();
                    })
                        ->orWhereNull('project_id');
                })
                ->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('change_status_task_status') && $row->system_default == 0) {
                        $isChecked = $row->active_status == 1 ? 'checked' : '';
                        $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;">
                            <input type="checkbox" class="switch-input changeStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    ';
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->addColumn('project', function ($row) {
                    return $row->project->title ?? '----------';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if ((hasPermission('update_task_status') || hasPermission('delete_task_status')) && $row->system_default == 0) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('update_task_status') && $row->system_default == 0) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }

                    if (hasPermission('delete_task_status') && $row->system_default == 0) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        $projects = Project::where('active_status', 1)->byShop('user_id')->get();
        return view('project-management.essentials.task-status.index', compact('projects'));
    }

    public function store(TaskStatusStoreRequest $request)
    {
        try {
            $count = TaskStatus::count();
            $data = new TaskStatus();
            $data->name = $request->name;
            $data->color = $request->color;
            $data->active_status = $request->status;
            $data->project_id = $request->project_id;
            $data->serial_number = $count;
            $data->save();
            return response()->json(['message' => 'Status create successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        $data = TaskStatus::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(TaskStatusUpdateRequest $request)
    {
        try {
            $data = TaskStatus::findOrFail($request->status_id);
            $data->name = $request->name;
            $data->color = $request->color;
            $data->active_status = $request->status;
            $data->project_id = $request->project_id;
            $data->save();
            return response()->json(['message' => 'Status updated successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = TaskStatus::with('tasks')->where('id', $request->status_id)->first();
            if ($data->system_default == 1) {
                return response()->json(['text' => 'Task Status is system default. Can not be deleted', 'icon' => 'error', 'title' => 'error', 'status' => 201]);
            }
            if ($data->tasks != null && $data->tasks->count() > 0) {
                return response()->json(['text' => 'Task Status has related task. Can not be deleted', 'icon' => 'error', 'title' => 'error', 'status' => 201]);
            }

            $data->delete();
            return response()->json(['text' => 'Status deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = TaskStatus::findOrFail($request->id);
        if ($data->tasks != null && $data->tasks->count() > 0) {
            return response()->json(['message' => 'Task Status has related task. Can not change the status', 'icon' => 'error', 'title' => 'error', 'status' => 500]);
        }
        $data->active_status = !$data->active_status;
        $data->save();
        return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
    }

    public function updateSerial(Request $request)
    {
        $taskStatus = TaskStatus::find($request->status_id);
        $newOrder = $request->order;
        $taskStatus->serial_number = $newOrder;
        $taskStatus->save();

        $taskStatuses = TaskStatus::where('project_id', $taskStatus->project_id)
            ->where('id', '!=', $taskStatus->id)
            ->orderBy('serial_number')
            ->get();

        $order = 1;
        foreach ($taskStatuses as $status) {
            if ($order == $newOrder) {
                $order++;
            }
            $status->serial_number = $order;
            $status->save();
            $order++;
        }

        return response()->json(['message' => 'Status order updated successfully.']);
    }
}
