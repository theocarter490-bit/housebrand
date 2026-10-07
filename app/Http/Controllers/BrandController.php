<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest;
use App\Http\Requests\IdValidationRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Brand;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Brand::select('*')->with('createdBy', 'updatedBy')->latest();
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
                    if (hasPermission('brand_status_change') && ($row->created_by == getUserId() || getUserId() == Role::SUPER_ADMIN)) {
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
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })
                ->addColumn('action', function ($row) {

                    $btn = '';

                    if ((hasPermission('brand_update') || hasPermission('brand_delete')) && ($row->created_by == getUserId() || getUserId() == Role::SUPER_ADMIN)) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                      // Check if the user has permission to update the brand or is a SUPER_ADMIN
                    if (hasPermission('brand_update') && ($row->created_by == getUserId() || getUserId() == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item btn btn-primary brand_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBrandEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }

                    // Check if the user has permission to delete the brand or is a SUPER_ADMIN
                    if (hasPermission('brand_delete') && ($row->created_by == getUserId() || getUserId() == Role::SUPER_ADMIN)) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_delete_button justify-content-center btn-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        return view('brand.index');
    }


    public function store(BrandStoreRequest $request)
    {

        try {
            DB::beginTransaction();
            $brand = new Brand();

            $brand->name = $request->name;
            $brand->description = $request->description;
            $brand->active_status = $request->status == '1' ? 1 : 0;
            $brand->slug = Str::slug($request->name);
            $brand->created_by = getUserId();
            $brand->updated_by = getUserId();

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'brand');
                $brand->image = $path;
            }

            $brand->save();
            DB::commit();
            removeDataFromRedisAPI(['product_list']);

            return response()->json(['message' => 'Brand Created Successfully', 'status' => 200]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function edit($id)
    {
        $brand = Brand::find($id);

        $brand = [
            'id' => $brand->id,
            'name' => $brand->name,
            'description' => $brand->description,
            'image' => getFilePath($brand->image),
            'active_status' => $brand->active_status,
        ];

        return response()->json(['data' => $brand, 'status' => 200]);
    }


    public function update(BrandUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $brand = Brand::find($request->brand_id);

            $brand->name = $request->name;
            $brand->description = $request->description;
            $brand->active_status = $request->status == '1' ? 1 : 0;
            $brand->slug = Str::slug($request->name);
            $brand->updated_by = getUserId();

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'brand');

                if ($brand->image) {
                    $this->deleteFile($brand->image);
                }
                $brand->image = $path;
            }

            $brand->save();
            DB::commit();
            removeDataFromRedisAPI(['product_list']);
            return response()->json(['message' => 'Brand Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $brand = Brand::with('products')->where('id', $request->brand_id)->first();
            if ($brand->products->count() > 0) {
                return response()->json(['text' => "This brand has products. You can't delete this brand.", 'icon' => 'warning', 'status' => 400]);
            }

            if ($brand->image != null && $brand->image) {
                $this->deleteFile($brand->image);
            }

            $brand->delete();
            removeDataFromRedisAPI(['product_list']);
            return response()->json(['text' => 'Brand has been deleted Successfully.', 'icon' => 'success', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = Brand::find($request->id);
        if ($data) {
            $data->active_status = !$data->active_status;
            $data->save();
            removeDataFromRedisAPI(['product_list']);
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }

}
