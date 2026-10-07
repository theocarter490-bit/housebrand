<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\ProjectCategoryStoreRequest;
use App\Http\Requests\ProjectCategoryUpdateRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Category;
use App\Models\ProjectCategory;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProjectCategoryController extends Controller
{
    //
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProjectCategory::select('*')->byShop('user_id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    return "<img src='" . getFilePath($row->image) . "' alt='' width='50px' height='50px' />";
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('change_status_essentials_category')) {
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

                    if (hasPermission('update_essentials_category') || hasPermission('delete_essentials_category')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }


                    if (hasPermission('update_essentials_category')){
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }


                    if (hasPermission('delete_essentials_category')){
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        return view('project-management.project.category.index');
    }

    public function store(ProjectCategoryStoreRequest $request)
    {
        try {

            $category = new ProjectCategory();

            $category->name = $request->name;
            $category->description = $request->description;
            $category->active_status = $request->status == '1' ? 1 : 0;
            $category->slug = createSlug($request->name);
            $category->user_id = getUserId();

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'project/category');
                $category->image = $path;
            }

            $category->save();

            return response()->json(['message' => 'Category Created Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $category = ProjectCategory::find($id);
        $category = [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'status' => $category->active_status,
            'image' => getFilePath($category->image),
        ];

        return response()->json(['data' => $category, 'status' => 200], 200);
    }


    public function update(ProjectCategoryUpdateRequest $request)
    {
        try {
            $category = ProjectCategory::find($request->category_id);

            $category->name = $request->name;
            $category->description = $request->description;
            $category->active_status = $request->status == '1' ? 1 : 0;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'project/category');

                if ($category->image) {
                    $this->deleteFile($category->image);
                }
                $category->image = $path;
            }

            $category->save();

            return response()->json(['message' => 'Category Updated Successfully', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $category = ProjectCategory::with(['projects'])->find($request->category_id);
            if ($category->projects->count() > 0) {
                return response()->json(['text' => 'This category has projects. Please delete the projects first.', 'icon' => 'error']);
            }

            if ($category->image) {
                $this->deleteFile($category->image);
            }

            $category->delete();
            return response()->json(['text' => 'Project Category has been deleted.', 'icon' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = ProjectCategory::find($request->id);
        if ($data) {
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }
}
