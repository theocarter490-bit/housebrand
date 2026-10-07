<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogCategoryCreateRequest;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BlogCategory::select('*')->isClient('user_id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('blog_category_status_change')) {
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
                    if ((hasPermission('blog_category_update') || hasPermission('blog_category_delete')) && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('blog_category_update') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                    }
                    if (hasPermission('blog_category_delete') && ($row->user_id == getUserId() || Auth::user()->role_id == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('blog.category.index');
    }

    public function store(BlogCategoryCreateRequest $request)
    {
        try {
            $blogCategory               = new BlogCategory();
            $blogCategory->name         = $request->name;
            $blogCategory->slug         = Str::slug($request->name);
            $blogCategory->user_id      = getUserId();
            $blogCategory->is_active    = $request->status;

            $blogCategory->save();
            return response()->json(['message' => 'Blog Category Created Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        $blogCategory = BlogCategory::find($id);
        return response()->json(['data' => $blogCategory, 'status' => 200]);
    }

    public function update(BlogCategoryUpdateRequest $request)
    {
        try {
            $blogCategory               = BlogCategory::find($request->id);
            $blogCategory->name         = $request->name;
            $blogCategory->slug         = Str::slug($request->name);
            $blogCategory->is_active    = $request->status;
            $blogCategory->update();
            return response()->json(['message' => 'Blog Category Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


    public function destroy(Request $request)
    {
        try {

            $category = BlogCategory::find($request->id);
            if (!$category) {
                return response()->json(['message' => 'Category not found', 'status' => 404]);
            }

            $relatedPostsCount = BlogPost::where('category_id', $category->id)->count();

            if ($relatedPostsCount > 0) {
                return response()->json(['message' => 'This category is used in blog posts. Delete related blog posts first.', 'status' => 400]);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted successfully', 'status' => 200]);

        } catch (Exception $e) {

            return response()->json(['message' => 'Something went wrong: ', 'status' => 500]);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = BlogCategory::find($request->id);
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
}
