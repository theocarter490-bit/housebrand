<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Requests\CouponStoreRequest;
use App\Http\Requests\CouponUpdateRequest;
use App\Http\Requests\IdValidationRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Coupon::select('*')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    $date = "Start Date:" . dateFormatwithTime($row->starts_at) . "<br>";
                    $date .= "Expires Date:" . dateFormatwithTime($row->expires_at);
                    return $date;

                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)

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
                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';


                    $btn .= '<a href="javascript:0;" class="dropdown-item category_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategoryEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';


                    // Check if the user has permission to delete the category or is a SUPER_ADMIN
                    if (hasPermission('category_delete') && ($row->created_by == getUserId() || getUserId() == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item category_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }


                    return $btn;
                })
                ->rawColumns(['action', 'status', 'date'])
                ->make(true);
        }

        return view('coupon.index');
    }

    public function store(CouponStoreRequest $request)
    {
        try {
            $category = new Coupon();

            $category->title = $request->name;
            $category->code = $request->code;
            $category->is_active = $request->status == '1' ? 1 : 0;
            $category->starts_at = $request->start_date;
            $category->expires_at = $request->end_date;

            $category->save();

            return response()->json(['message' => 'Coupon Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($id)
    {
        $category = Coupon::find($id);
        $category = [
            'id' => $category->id,
            'title' => $category->title,
            'code' => $category->code,
            'starts_at' => $category->starts_at,
            'expires_at' => $category->expires_at,
            'status' => $category->is_active,
        ];

        return response()->json(['data' => $category, 'status' => 200], 200);
    }


    public function update(CouponUpdateRequest $request)
    {

        try {

            $category = Coupon::find($request->category_id);

            $category->title = $request->name;
            $category->code = $request->code;
            $category->is_active = $request->status == '1' ? 1 : 0;
            $category->starts_at = $request->start_date;
            $category->expires_at = $request->end_date;
            $category->save();

            return response()->json(['message' => 'Coupon Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $coupon = Coupon::find($request->category_id);
            $coupon->delete();
            return response()->json(['text' => 'Coupon deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = Coupon::find($request->id);
        if ($data) {
            $data->is_active = !$data->is_active;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }
}
