<?php

namespace App\Http\Controllers;

use App\Models\NoticeType;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\StoreNoticeTypeRequest;
use App\Models\NoticeBoard;
use Exception;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class NoticeTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = NoticeType::byShop()->select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('notice_type_change_status')) {
                        $isChecked = $row->is_active == 1 ? 'checked' : '';
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
                        $instance->where('is_active', $request->get('status'));
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
        return view('notice-board.type.index');
    }

    public function store(StoreNoticeTypeRequest $request)
    {

        try {
            $noticeType = new NoticeType();
            $noticeType->name = $request->name;
            $noticeType->user_id = getUserId();
            $noticeType->is_active = $request->status;

            $noticeType->save();
            return response()->json(['message' => 'Notice Type Created Successfully', 'status' => 200]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = NoticeType::find($request->id);
        if ($data) {
            if ($data->is_active == 1) {
                $data->is_active = 0;
            } else {
                $data->is_active = 1;
            }
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }


    public function edit($id)
    {
        $noticeType = NoticeType::find($id);
        return response()->json(['data' => $noticeType, 'status' => 200]);
    }

    public function update(StoreNoticeTypeRequest $request)
    {
        try {
            $noticeType = NoticeType::find($request->id);
            $noticeType->name = $request->name;
            $noticeType->is_active = $request->status;
            $noticeType->update();
            return response()->json(['message' => 'Notice Type Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function destroy(IdValidationRequest $request)
    {
        try {

            $noticeType = NoticeType::find($request->id);

            if (!$noticeType) {
                return response()->json(['message' => 'Notice Type not found', 'status' => 404]);
            }

            $relatedNoticesCount = NoticeBoard::where('type_id', $noticeType->id)->count();

            if ($relatedNoticesCount > 0) {
                return response()->json(['message' => 'This Type is used in Notices. Delete related Notices first.', 'status' => 400]);
            }

            $noticeType->delete();

            return response()->json(['message' => 'Notice Type Deleted Successfully', 'status' => 200]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage(), 'status' => 500]);
        }
    }


}
