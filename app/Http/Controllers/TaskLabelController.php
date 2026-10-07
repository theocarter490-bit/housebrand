<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskLabelStoreRequest;
use App\Http\Requests\TaskLabelUpdateRequest;
use App\Models\TaskLabel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskLabelController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = TaskLabel::select('*')->byShop('user_id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('change_status_task_label')) {
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
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if (hasPermission('update_task_label') || hasPermission('delete_task_label')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';

                    }

                    if (hasPermission('update_task_label')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }

                    if (hasPermission('delete_task_label')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        return view('project-management.essentials.task-label.index');
    }

    public function store(TaskLabelStoreRequest $request)
    {
        try {
            $data = new TaskLabel();
            $data->name = $request->name;
            $data->color = $request->color;
            $data->active_status = $request->status;
            $data->user_id = getUserId();
            $data->save();
            return response()->json(['message' => 'Status Create Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        $data = TaskLabel::findOrFail($id);
        return response()->json(['data' => $data]);
    }

    public function update(TaskLabelUpdateRequest $request)
    {
        try {
            $data = TaskLabel::findOrFail($request->status_id);
            $data->name = $request->name;
            $data->color = $request->color;
            $data->active_status = $request->status;
            $data->save();
            return response()->json(['message' => 'Status updated successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = TaskLabel::findOrFail($request->status_id);

            if ($data->tasks != null && ($data->tasks->count() > 0)) {
                return response()->json(['text' => 'Task Label has task can not be deleted. It has active task', 'icon' => 'error', 'title' => 'error', 'status' => 200]);
            }
            $data->delete();
            return response()->json(['text' => 'Task label deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = TaskLabel::findOrFail($request->id);
        $data->active_status = !$data->active_status;
        $data->save();
        return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
    }

}
