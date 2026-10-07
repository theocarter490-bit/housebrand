<?php

namespace App\Http\Controllers;

use Exception;
use PSpell\Config;
use Illuminate\Http\Request;
use App\Models\BackgroundSetting;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\BackgroundSettingsStoreRequest;

class BackgroundSettingsController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BackgroundSetting::with('createdBy', 'updatedBy');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        return "<img src='" . getFilePath($row->image) . "' alt='' width='50px' height='50px' />";
                    }
                    return '';
                })
                ->addColumn('purpose', function ($row) {
                    return match ($row->purpose) {
                        0 => '<span class="badge bg-label-success">Login Page</span>',
                        1 => '<span class="badge bg-label-info">Signup Page</span>',
                        2 => '<span class="badge bg-label-primary">Admin Login Page</span>',
                        3 => '<span class="badge bg-label-warning">Forget Password</span>',
                        4 => '<span class="badge bg-label-danger">Reset Password</span>',
                    };
                })
                ->addColumn('type', function ($row) {
                    if ($row->type == 'image') {
                        return '<span class="badge bg-label-success">Image</span>';
                    }
                    return '<span class="badge bg-label-primary">Color</span>';
                })
                ->addColumn('color', function ($row) {
                    if ($row->color) {
                        return $row->color;
                    }
                    return '';
                })

                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('is_active', $request->get('status'));
                    }
                    if ($request->get('type') != '') {
                        $instance->where('type', $request->get('type'));
                    }
                }, true)
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('background_settings_status_change')) {
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
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if ($row->is_active == 0) {
                        if (hasPermission('background_settings_update') || hasPermission('background_settings_delete')) {
                            $btn = '<div class="d-inline-block text-nowrap">' .
                                '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                                '<div class="dropdown-menu dropdown-menu-end m-0">';
                        }
                        if (hasPermission('background_settings_update')) {
                            $btn .= '<a href="javascript:0;" class="dropdown-item edit_button" data-bs-toggle="modal" data-bs-target="#editSettingModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>';
                        }
                        if (hasPermission('background_settings_delete')) {
                            $btn .= '<a href="javascript:0;" class="dropdown-item setting_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                                '</div>' .
                                '</div>';
                        }
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'type', 'info', 'purpose', 'color'])
                ->make(true);
        }
        return view('setting.background-setting');
    }

     public function changeStatus(Request $request){
        $request->validate([
            'id' => 'required|exists:background_settings,id',
        ]);
        $data = BackgroundSetting::find($request->id);
        try {
            if ($data->is_active == 1) {
                $data->is_active = 0;
            } else {
                $data->is_active = 1;
            }
            $data->save();
            Cache::forget("login_bg");
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something Went Wrong!"], 500);
        }
    }

    public function delete(Request $request)
    {
        $data = BackgroundSetting::find($request->id);
        try {
            if ($data->is_active == 0) {
                $data->delete();
                return response()->json(['message' => 'Setting Deleted Successfully', 'status' => 200, 'icon'=>'success'], 200);
            }
            Cache::forget("login_bg");
        } catch (Exception $e) {
            return response()->json(['message' => "Something Went Wrong!"], 500);
        }
    }

    public function store(BackgroundSettingsStoreRequest $request)
    {
        try {
            $settings = new BackgroundSetting();
            $this->setOrUpdateSetting($request, $settings);
            $settings->created_by = Auth::user()->id;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'background-settings');
                $settings->image = $path;
            } else if ($request->color) {
                $settings->color = $request->color;
            }
            $settings->save();
            Cache::forget("login_bg");
            Toastr::success('Settings Created Successfully');
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
        return back();
    }

    public function edit($id)
    {
        $data = BackgroundSetting::find($id);
        $data = [
            'id' => $data->id,
            'title' => $data->title,
            'short_desc' => $data->short_desc,
            'purpose' => $data->purpose,
            'type' => $data->type,
            'color' => $data->color,
            'image' => getFilePath($data->image),
        ];
        if ($data) {
            return response()->json(['data' => $data, 'status' => 200], 200);
        }
        return response()->json(['message' => "Settings not found"], 404);
    }

    public function update(BackgroundSettingsStoreRequest $request)
    {

        try {
            $settings = BackgroundSetting::find($request->id);
            $this->setOrUpdateSetting($request, $settings);
            $settings->updated_by = Auth::user()->id;
            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'gallery');
                if ($settings->image) {
                    $this->deleteFile($settings->image);
                }
                $settings->image = $path;

                if ($settings->color) {
                    $settings->color = '';
                }
            } else if ($request->color) {
                $settings->color = $request->color;
                if ($settings->image) {
                    $this->deleteFile($settings->image);
                    $settings->image = '';
                }
            }
            $settings->save();
            Cache::forget("login_bg");
            Toastr::success('Settings Update Successfully');
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong!');
        }
        return back();
    }

    public function setOrUpdateSetting(BackgroundSettingsStoreRequest $request, BackgroundSetting $settings)
    {
        $settings->title = $request->title;
        $settings->short_desc = $request->description;
        $settings->purpose = $request->purpose;
        $settings->type = $request->type;
    }
}
