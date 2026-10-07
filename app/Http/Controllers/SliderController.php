<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderStoreRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Slider;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{

    use FileUploadTrait;


    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Slider::where('user_id', getUserId());
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        return getFileElement(getFilePath($row->image));
                    }
                    return '';
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('slider_status_change')) {
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
                ->editColumn('type', function ($row) {
                    return match ($row->type) {
                        0 => '<span class="badge bg-label-info">Hero Section</span>',
                        1 => '<span class="badge bg-label-primary">About US</span>',
                        2 => '<span class="badge bg-label-warning">Banner with Product</span>',
                    };
                })
                ->editColumn('file_type', function ($row) {
                    return $row->file_type == 0 ? '<span class="badge bg-label-info">Image</span>' : '<span class="badge bg-label-primary">Video</span>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                    if ($request->get('type') == '0' || $request->get('type') == '1' || $request->get('type') == '2') {
                        $instance->where('type', $request->get('type'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('slider_update') || hasPermission('slider_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('slider_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item slider_edit_button" data-bs-toggle="modal" data-bs-target="#editSliderModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                    }
                    if (hasPermission('slider_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item slider_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'description', 'status', 'image', 'type', 'file_type'])
                ->make(true);
        }
        return view('frontend-cms.slider.index');
    }

    public function store(SliderStoreRequest $request)
    {
        try {
            $data = new Slider();
            $data->title = $request->title;
            $data->description = $request->description;
            $data->user_id = getUserId();
            $data->type = $request->slider_type;
            $data->active_status = $request->active_status;
            $data->file_type = $request->file_type;
            if ($request->hasFile('image')) {
                $data->image = $this->uploadFile($request->file('image'), 'slider', 'default');
            }
            $productList = [];
            if ($request->TagifyProductList) {

                foreach (json_decode($request->TagifyProductList) as $tag) {
                    array_push($productList, [
                        'id' => (int)$tag->value,
                        'x' => $tag->coordinate->x,
                        'y' => $tag->coordinate->y,
                    ]);
                }
            }
            $data->product_ids = $productList;
            $data->save();
            Toastr::success('Slider Created Successfully');
            removeDataFromRedisAPI(['slider_list', getUserId()]);

        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        try {
            $data = Slider::with('products')->where('id', $id)->first();
            if ($data) {
                $data->image = getFilePath($data->image);
                return response()->json(['data' => $data, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Slider not found'], 404);
            }
        } catch (\Exception $exception) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'editTitle' => 'nullable|string',
            'image' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $edit_file_type = request()->input('edit_file_type');
                    $image = request()->file('image');

                    if ($edit_file_type == 0 && $image && !in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                        $fail('The image must be a valid image file (jpeg, png, jpg, webp) when file_type is image.');
                    }

                    if ($edit_file_type == 1 && $image && !in_array($image->getMimeType(), ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'])) {
                        $fail('The image must be a valid video file (mp4, mov, avi, mkv) when file_type is video.');
                    }
                },
            ],
            'description' => 'string|nullable',
            'edit_slider_type' => 'required',
            'edit_file_type' => 'required|in:0,1',
            'editStatus' => 'required',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                Toastr::error($value[0]);
            }
            return redirect()->back();
        }


        try {
            $data = Slider::find($request->slider_id);
            if ($data) {
                if ($request->hasFile('image')) {
                    $this->deleteFile($data->image);
                    $data->image = $this->uploadFile($request->file('image'), 'slider', 'default');
                }
                $data->title = $request->editTitle;
                $data->description = $request->description;
                $data->active_status = $request->editStatus;
                $data->type = $request->edit_slider_type;
                $data->file_type = $request->edit_file_type;
                $productList = [];
                if ($request->TagifyProductList) {

                    foreach (json_decode($request->TagifyProductList) as $tag) {
                        array_push($productList, [
                            'id' => (int)$tag->value,
                            'x' => $tag->coordinate->x,
                            'y' => $tag->coordinate->y,
                        ]);
                    }
                }
                $data->product_ids = $productList;
                $data->save();
                Toastr::success('Slider Updated Successfully');
                removeDataFromRedisAPI(['slider_list', getUserId()]);
            }
        } catch (\Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
        return redirect()->back();
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = Slider::find($request->id);
            if ($data) {
                $data->active_status = !$data->active_status;
                $data->save();
                removeDataFromRedisAPI(['slider_list', getUserId()]);
                return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = Slider::find($request->id);
            if ($data->active_status == 1) {
                return response()->json(['message' => "You can’t delete an active slide. Please deactivate it first before deleting.", 'status' => 200], 500);
            }

            if ($data) {
                if ($data->image) {
                    $this->deleteFile($data->image);
                }
                $data->delete();
                removeDataFromRedisAPI(['slider_list', getUserId()]);
                Toastr::success(['success', 'Slider Deleted Successfully', 'status' => 200]);
                return response()->json(['message' => "Slider Deleted Successfully.", 'status' => 200], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'status' => 200], 500);
        }
    }
}
