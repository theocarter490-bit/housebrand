<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTypeStoreRequest;
use App\Http\Requests\ReviewTypeUpdateRequest;
use App\Models\ReviewType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReviewTypeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ReviewType::select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('notice_type_change_status')) {
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
                ->addColumn('type', function ($row) {
                    return $row->type == 1 ? 'Products' : 'Shop';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('notice_type_delete') || hasPermission('notice_type_update') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('notice_type_update') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';

                    }

                    if (hasPermission('notice_type_delete') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('designer-reviews.review-types.index');
    }

    public function store(ReviewTypeStoreRequest $request)
    {
        try {
            $reviewType = new ReviewType();
            $reviewType->name = $request->name;
            $reviewType->type = $request->type;
            $reviewType->active_status = $request->status;
            $reviewType->created_by = getUserId();
            $reviewType->save();
            return response()->json(['message' => 'Review Type Created Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        try {
            $data = ReviewType::where('id', $id)->first();
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function update(ReviewTypeUpdateRequest $request)
    {
        try {
            $data = ReviewType::where('id', $request->id)->first();
            $data->name = $request->name;
            $data->active_status = $request->status;
            $data->type = $request->type;
            $data->save();
            return response()->json(['message' => 'Review type Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = ReviewType::findOrFail($request->id);
            $data->delete();
            return response()->json(['message' => 'Review type Deleted Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Review Has associate data!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = ReviewType::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

}
