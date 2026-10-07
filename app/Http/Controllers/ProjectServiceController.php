<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignUserStoreRequest;
use App\Http\Requests\ProjectServiceStoreRequest;
use App\Http\Requests\ProjectServiceUpdateRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\AssignProjectService;
use App\Models\ProjectService;
use App\Models\ServiceCategory;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ProjectServiceController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        $data = ProjectService::with('category')
            ->where('user_id', getUserId())->latest();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category', function ($row) {
                    return $row->category->name;
                })
                ->addColumn('image', function ($row) {
                    return getFileElement(getFilePath($row->image));
                })
                ->editColumn('tax_type', function ($row) {
                    return $row->tax_type == 1 ? 'Percentage' : 'Fixed';
                })
                ->editColumn('tax', function ($row) {
                    if ($row->tax_type == 1) {
                        return "{$row->tax}%";
                    } else {
                        return getPriceFormat($row->tax);
                    }
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('change_status_services')) {
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

                    if (hasPermission('update_services') || hasPermission('delete_services')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('assign_user_service_setup')) {
                        $btn .= '<a href="' . route('project-management.service.assign-user.index', $row->id) . '" class="dropdown-item assign_user text-primary" data-id="' . $row->id . '"><i class="ti ti-users-plus"></i> ' . _trans('keyword.Assign Employee') . '</a>';
                    }

                    if (hasPermission('update_services')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item editServiceModal" data-bs-toggle="modal" data-bs-target="#editServiceModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }

                    if (hasPermission('delete_services')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        $serviceCategories = ServiceCategory::where('active_status', 1)
            ->where('user_id', getUserId())->get();
        return view('project-management.service.index', compact('serviceCategories'));
    }


    public function store(ProjectServiceStoreRequest $request)
    {

        try {
            $data = new ProjectService();
            $data->title = $request->title;
            $data->slug = createSlug($request->title);
            if ($request->hasFile('image')) {
                $data->image = $this->uploadFile($request->file('image'), 'project/service');
            }
            $data->cost = $request->cost;
            $data->description = $request->description;
            $data->tax_type = $request->tax_type;
            $data->tax = $request->tax;
            $data->service_category_id = $request->service_category_id;
            $data->user_id = Auth::user()->id;
            $data->active_status = $request->active_status;
            $data->save();
            Toastr::success('Project service added successfully');
        } catch (Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }

    public function edit($id)
    {
        $data = ProjectService::findOrFail($id);
        $data->image = getFilePath($data->image);

        return response()->json($data);
    }

    public function update(ProjectServiceUpdateRequest $request)
    {
        try {
            $data = ProjectService::findOrFail($request->service_id);
            if ($data->titlle != $request->title) {
                $data->slug = createSlug($request->title);
            }
            $data->title = $request->title;
            if ($request->hasFile('image')) {
                $data->image = $this->uploadFile($request->file('image'), 'project/service');
            }
            $data->cost = $request->cost;
            $data->description = $request->description;
            $data->tax_type = $request->tax_type;
            $data->tax = $request->tax;
            $data->service_category_id = $request->service_category_id;
            $data->active_status = $request->active_status;
            $data->save();
            Toastr::success('Project service update successfully');
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!' . $e->getMessage());
        }
        return back();
    }

    public function delete(Request $request)
    {
        try {
            $data = ProjectService::with('assignUsers')->where('id', $request->id)->first();
            if ($data->assignUsers->count() > 0) {
                return response()->json(['text' => 'Project service has assigned user. Can not be deleted.', 'icon' => 'warning', 'title' => 'warning', 'status' => 200]);
            }
            $data->delete();
            return response()->json(['text' => 'Project service deleted successfully', 'icon' => 'success', 'title' => 'success', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = ProjectService::findOrFail($request->id);
        $data->active_status = !$data->active_status;
        $data->save();
        return response()->json(['message' => 'Status change successfully', 'status' => 200]);
    }

    public function assignUser(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = AssignProjectService::with('user')
                ->where('project_service_id', $id)
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    $avatarUrl = getFilePath($row->user->avatar);

                    return '<img src="'.$avatarUrl.'" alt="User Avatar" width="50" height="50" class="rounded-circle" > ';
                })
                ->addColumn('info', function ($row) {
                    if ($row->user) {
                        return '<strong>Name:</strong >' . $row->user->name . '<br> ' .
                            '<strong> Email:</strong> ' . $row->user->email;
                    }
                    return 'N / A';
                })
                ->addColumn('fee', function ($row) {
                    return getPriceFormat($row->fee);
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? ' custom-bg-success' : ' custom-bg-danger';

                    $statusHtml = ' <div class="custom-status-container"> ';
                    $isChecked = $row->active_status == 1 ? 'checked' : '';
                    $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;" >
                            <input type= "checkbox" class="switch-input changeStatus" data-id = "'. $row->id .'" '. $isChecked.' />
                            <span class="switch-toggle-slider" >
                                <span class="switch-on"><i class="ti ti-check" ></i ></span>
                                <span class="switch-off"><i class="ti ti-x" ></i ></span>
                            </span>
                        </label>
                ';
                    $statusHtml .= '<div><span class="badge' .$statusBadgeClass. '" > ' . $statusLabel . '</span></div>';
                    $statusHtml .= '</div>';

                    return $statusHtml;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-inline-block text-nowrap" >
                        <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle = "dropdown" ><i class="ti ti-dots-vertical me-2" ></i ></button >
                        <div class="dropdown-menu dropdown-menu-end m-0" >
                             <a href="javascript:void(0);" class="dropdown-item user_edit_button text-primary" data-id="'.$row->id . '" ><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . ' </a>
                            <a href="javascript:void(0);" class="dropdown-item user_delete_button text-danger" data-id="'.$row->id .'" ><i class="ti ti-trash"></i> ' . _trans('keyword.Remove') . ' </a>
                        </div>
                    </div> ';
                })
                ->rawColumns(['action', 'status', 'image', 'info'])
                ->make(true);
        }

        $service = ProjectService::findOrFail($id);
        $userId = $service->user_id;
        $users = User::where('supervisor_id', $userId)
            ->where('active_status', 1)
            ->get();
        return view('project-management.service.assign-user.index', compact('service', 'users'));
    }

    public function storeAssignUser(AssignUserStoreRequest $request)
    {
        try {
            $data = AssignProjectService::where('user_id', $request->user_id)
                ->where('project_service_id', $request->service_id)->first();

            if ($data) {
                $data->fee = $request->service_fee;
            } else {
                $data = new AssignProjectService();
                $data->name = $request->service_name;
                $data->fee = $request->service_fee;
                $data->user_id = $request->user_id;
                $data->project_service_id = $request->service_id;
            }
            $data->save();
            return response()->json(['message' => 'Assign user successfully . ', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }

    }

    public function editAssignUser($id)
    {
        $data = AssignProjectService::findOrFail($id);
        return response()->json($data);
    }

    public function deleteAssignUser(Request $request)
    {
        try {
            $service = AssignProjectService::with('timeBilling')->where('id', $request->id)->first();
            if ($service->timeBilling != null && $service->timeBilling->count() > 0) {
                return response(['message' => 'This User has time billing data . Can not be deleted . ', 'status' => 200]);
            }
            $service->delete();
            return response(['message' => 'User removed successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function assignUserChangeStatus(Request $request)
    {
        try {
            $data = AssignProjectService::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }

    }

}
