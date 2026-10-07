<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\SectionCategoryStoreRequest;
use App\Http\Requests\SectionCategoryUpdateRequest;
use App\Models\Product;
use App\Models\Role;
use App\Models\SpecialSection;
use App\Models\SpecialSectionCategory;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SpecialSectionCategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = SpecialSectionCategory::select('*')->byShop('created_by')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('section_category_status_change') && (!isSeller() || (isSeller() && $row->user_id == Auth::id()))) {
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
                    if ((hasPermission('section_category_update') || hasPermission('section_category_delete')) && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('section_category_update') && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';

                    }
                    if (hasPermission('section_category_delete') && ($row->created_by == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        return view('section.section-category.index');
    }


    public function store(SectionCategoryStoreRequest $request)
    {
        try {

            $sectionCategory = new SpecialSectionCategory();

            $sectionCategory->name = $request->name;
            $sectionCategory->slug = Str::slug($request->name);
            $sectionCategory->user_id = getUserId();
            $sectionCategory->created_by = Auth::user()->id;
            $sectionCategory->is_active = $request->status;
            $sectionCategory->save();

            return response()->json(['message' => 'Section Category Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $sectionCategory = SpecialSectionCategory::find($id);

        return response()->json(['data' => $sectionCategory, 'status' => 200], 200);
    }


    public function update(SectionCategoryUpdateRequest $request)
    {
        try {

            $sectionCategory = SpecialSectionCategory::find($request->special_sections_category_id);

            $sectionCategory->name = $request->name;
            $sectionCategory->is_active = $request->status == '1' ? 1 : 0;
            $sectionCategory->slug = Str::slug($request->name);
            $sectionCategory->updated_by = Auth::user()->id;

            $sectionCategory->save();

            return response()->json(['message' => 'Section Category Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $sectionCategory = SpecialSectionCategory::find($request->special_sections_category_id);
            $countSpecialSection = SpecialSection::where('special_section_category_id', $sectionCategory->id)->count();
            if ($countSpecialSection) {
                return response()->json(['text' => 'Section Category has special section and can not be deleted.', 'icon' => 'error']);
            }
            $sectionCategory->delete();
            return response()->json(['text' => 'Section Category has been deleted.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = SpecialSectionCategory::find($request->id);
        if ($data) {
            $data->is_active = !$data->is_active;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }


    public function userProduct()
    {
        $products = Product::whereUserId(getUserId())->get();
        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail_img' => getFilePath($product->thumbnail_img),
            ];
        });
        return response()->json([$products]);
    }

    public function getCategories()
    {
        $categories = SpecialSectionCategory::byShop('created_by')->latest()->get();
        return response()->json(['categories' => $categories, 'status' => 200], 200);
    }

}
