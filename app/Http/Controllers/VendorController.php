<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\VendorStoreRequest;
use App\Http\Requests\VendorUpdateRequest;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Vendor::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('manufacturer_status_change')) {
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
                    if ($request->get('active_status') == '0' || $request->get('active_status') == '1') {
                        $instance->where('is_active', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if (hasPermission('manufacturer_update') || hasPermission('manufacturer_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('manufacturer_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item vendor_edit_button" data-bs-toggle="modal" data-bs-target="#modalCenterEdit" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> ' . _trans('keyword.Edit') . '</a>';
                    }
                    if (hasPermission('manufacturer_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item vendor_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('vendor.index');
    }


    public function store(VendorStoreRequest $request)
    {
        try {

            $vendor = new Vendor();

            $vendor->name = $request->name;
            $vendor->description = $request->description;
            $vendor->address = $request->address;
            $vendor->city = $request->city;
            $vendor->state = $request->state;
            $vendor->postal_code = $request->postal_code;
            $vendor->website = $request->vendor_website;
            $vendor->contact_name = $request->contact_name;
            $vendor->mobile = $request->mobile;
            $vendor->phone = $request->phone;
            $vendor->is_active = $request->status == '1' ? 1 : 0;

            $vendor->save();

            return response()->json(['message' => 'Manufacturer Created', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $vendor = Vendor::find($id);

        return response()->json(['data' => $vendor, 'status' => 200], 200);
    }


    public function update(VendorUpdateRequest $request)
    {

        try {

            $vendor = Vendor::find($request->vendor_id);

            $vendor->name = $request->name;
            $vendor->description = $request->description;
            $vendor->address = $request->address;
            $vendor->city = $request->city;
            $vendor->state = $request->state;
            $vendor->postal_code = $request->postal_code;
            $vendor->website = $request->vendor_website;
            $vendor->contact_name = $request->contact_name;
            $vendor->mobile = $request->mobile;
            $vendor->phone = $request->phone;
            $vendor->is_active = $request->status == '1' ? 1 : 0;

            $vendor->save();

            return response()->json(['message' => 'Manufacturer Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $vendor = Vendor::find($request->vendor_id);

            $vendor->delete();

            return response()->json(['text' => 'Manufacturer has been deleted.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = Vendor::find($request->id);
        if ($data) {
            $data->is_active = !$data->is_active;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }
}
