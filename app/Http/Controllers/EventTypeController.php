<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventTypeStoreRequest;
use App\Http\Requests\EventTypeUpdateRequest;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Role;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class EventTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EventType::where('user_id', getUserId())->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('event_type_change_status')) {
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
                    if ($request->get('active_status') == '0' || $request->get('active_status') == '1') {

                        $instance->where('active_status', (int)$request->get('active_status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if ((hasPermission('event_type_delete') || hasPermission('event_type_update')) && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('event_type_update') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                    }

                    if (hasPermission('event_type_delete') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item type_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';

                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view("event-management.type.index");

    }

    public function store(EventTypeStoreRequest $request)
    {
        try {
            $type = new EventType();
            $type->name = $request->name;
            $type->color = $request->color;
            $type->active_status = $request->active_status;
            $type->user_id = getUserId();
            $type->save();
            Toastr::success('Event Type Added Successfully');
        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!', 'Error');
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        try {
            $data = EventType::where('id', $id)->first();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json('Something Went Wrong!', 'Error');
        }
    }

    public function update(EventTypeUpdateRequest $request)
    {
        try {
            $data = EventType::where('id', $request->event_type_id)->first();
            $data->name = $request->name;
            $data->color = $request->color;
            $data->active_status = $request->active_status;
            $data->save();
            Toastr::success('Event Type Updated Successfully');
        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!', 'Error');
        }
        return redirect()->back();
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = EventType::where('id', $request->id)->first();
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!', 'status' => 500]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $event = Event::where('event_type_id', $request->id)->first();
            if ($event) {
                return response()->json(['message' => 'This Event Type is Currently in Use!', 'status' => 500]);
            }
            $data = EventType::where('id', $request->id)->first();
            $data->delete();
            return response()->json(['message' => 'Event Type Deleted Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!', 'status' => 500]);
        }
    }

}
