<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Models\Product;
use App\Models\Role;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Unit::select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('unit_status_change')) {
                        if (($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)){

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
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })

                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('is_active', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if ((hasPermission('unit_update') || hasPermission('unit_delete'))  && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN) ) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('unit_update')  && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN) ) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item unit_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUnitEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>' .
                            '<div class="dropdown-divider"></div>';
                    }

                    if (hasPermission('unit_delete')  && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item unit_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'info'])
                ->make(true);
        }

        return view('unit.index');
    }

    public function store(UnitStoreRequest $request)
    {
        try {

            $attribute = new Unit();
            $attribute->name = $request->name;
            $attribute->is_active = $request->status == '1' ? 1 : 0;
            $attribute->created_by  = getUserId();
            $attribute->updated_by  = getUserId();
            $attribute->save();

            return response()->json(['message' => 'Unit Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $unit = Unit::find($id);

        return response()->json(['data' => $unit, 'status' => 200], 200);
    }


    public function update(UnitUpdateRequest $request)
    {
        try {

            $Attribute = Unit::find($request->unit_id);

            $Attribute->name = $request->name;
            $Attribute->is_active = $request->status == '1' ? 1 : 0;
            $Attribute->updated_by = getUserId();
            $Attribute->save();

            return response()->json(['message' => 'Unit Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $unit = Unit::find($request->unit_id);

            $products = Product::whereUnit($unit->name)->first();
            if ($products){
                return response()->json(['text' => "This unit has products. You can't delete this unit.", 'icon' => 'error', 'status'=> 500]);
            }
            $unit->delete();
            return response()->json(['text' => 'Unit has been deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = Unit::find($request->id);
        if ($data) {
            $data->is_active = !$data->is_active;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }

}
