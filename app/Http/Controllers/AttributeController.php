<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeUpdateRequest;
use App\Http\Requests\IdValidationRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AttributeController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Attribute::select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('attribute_status_change') && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $isChecked = $row->status == 1 ? 'checked' : '';
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
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })

                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('status', $request->get('status'));
                    }
                }, true)

                ->addColumn('action', function ($row) {

                    $btn = '';
                    if ((hasPermission('attribute_value_create') || hasPermission('attribute_update') || hasPermission('attribute_delete')) ) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('attribute_value_create')) {
                        $btn .= '<a href="' . route('attribute.value.index', $row->id) . '" class="dropdown-item text-primary" data-bs-toggle="tooltip" title="Add attribute value"><i class="ti ti-plus"></i>' . _trans('keyword.Add') .' '._trans('keyword.Value') .'</a>';
                    }
                    if (hasPermission('attribute_update')  && $row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item attribute_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBrandEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> ' . _trans('keyword.Edit') . '</a>';
                    }
                    if (hasPermission('attribute_delete')  && $row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN) {
                        $btn .= '<div class="dropdown-divider"></div>' .
                            '<a href="javascript:0;" class="dropdown-item attribute_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'info'])
                ->make(true);
        }

        return view('attribute.index');
    }

    public function store(AttributeStoreRequest $request)
    {
        try {

            $attribute = new Attribute();

            $attribute->name = $request->name;
            $attribute->description = $request->description;
            $attribute->status = $request->status == '1' ? 1 : 0;
            $attribute->slug = Str::slug($request->name);
            $attribute->created_by  = getUserId();
            $attribute->updated_by  = getUserId();
            $attribute->save();

            return response()->json(['message' => 'Attribute Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $attribute = Attribute::find($id);

        return response()->json(['data' => $attribute, 'status' => 200], 200);
    }


    public function update(AttributeUpdateRequest $request)
    {
        try {

            $Attribute = Attribute::find($request->attribute_id);

            $Attribute->name = $request->name;
            $Attribute->description = $request->description;
            $Attribute->status = $request->status == '1' ? 1 : 0;
            $Attribute->slug = Str::slug($request->name);
            $Attribute->updated_by = getUserId();
            $Attribute->save();

            return response()->json(['message' => 'Attribute Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $products = Product::whereJsonContains('attributes', $request->attribute_id)->get();
            if (count($products) > 0) {
                return response()->json(['text' => 'This attribute is already used in product. So, you can not delete this attribute.', 'icon' => 'error']);
            }
            $attribute = Attribute::find($request->attribute_id);

            if ($attribute->image != null && $attribute->image) {
                $this->deleteFile($attribute->image);
            }

            $attribute->delete();
            return response()->json(['text' => 'Attribute has been deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = Attribute::find($request->id);
        if ($data) {
            $data->status = !$data->status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }

}
