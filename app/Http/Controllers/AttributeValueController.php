<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\AttributeValueStoreRequest;
use App\Http\Requests\AttributeValueUpdateRequest;

class AttributeValueController extends Controller
{
    public function index(Request $request, $attribute_id)
    {

        if ($request->ajax()) {
            $data = AttributeValue::select('*')->where('attribute_id',$attribute_id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn =

                        '<div class="d-flex align-items-center">' .
                        '<div class="d-inline-block">' .
                        '<a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm lh-1"></i></a>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';
                        if ((hasPermission('attribute_value_update')  && $row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)){
                            $btn.='<p  class="dropdown-item"><button  class="btn btn-sm btn-icon attribute_value_edit_button w-100" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAttributeValueEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</button></p>'.
                                '<div class="dropdown-divider"></div>';
                        }

                        if ((hasPermission('attribute_value_delete')  && $row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)){
                            $btn.='<p  class="dropdown-item text-danger delete-record"><button  class="btn btn-sm btn-icon text-danger delete-record attribute_value_delete_button w-100"  data-id="' . $row->id . '"  ><i class="ti ti-trash"></i> Delete</button></p>' .
                                '</div>' .
                                '</div>' .
                                '</div>';
                        }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $attribute = Attribute::find($attribute_id);
        return view('attribute.values', compact('attribute'));
    }

    public function store(AttributeValueStoreRequest $request)
    {

        try {

            $attributeValue = new AttributeValue();

            $attributeValue->name = $request->name;
            $attributeValue->value = $request->aditional_value;
            $request->check_color == 'true'? $attributeValue->value = $request->aditional_value : $attributeValue->value = null;
            $attributeValue->attribute_id = $request->attribute_id;
            $attributeValue->created_by = Auth::user()->id;
            $attributeValue->save();

            return response()->json(['message' => 'Attribute Value Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $attributeValue = AttributeValue::find($id);

        return response()->json(['data' => $attributeValue, 'status' => 200], 200);
    }


    public function update(AttributeValueUpdateRequest $request)
    {
        try {

            $attributeValue = AttributeValue::find($request->value_id);
            $attributeValue->name = $request->name;
            $request->check_color == 'true'? $attributeValue->value = $request->aditional_value : $attributeValue->value = null;
            $attributeValue->updated_by = Auth::user()->id;
            $attributeValue->save();

            return response()->json(['message' => 'Attribute Value Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $attributeValue = AttributeValue::find($request->value_id);
            $attributeValue->delete();

            return response()->json(['text' => 'Attribute Value has been deleted successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }
}
