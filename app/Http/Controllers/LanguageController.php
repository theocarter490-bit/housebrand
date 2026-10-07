<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Locale;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\LanguageSettingStoreRequest;

class LanguageController extends Controller
{

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Language::all();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('rtl', function ($row) {
                    $isActive = $row->rtl == 1 ? 'checked' : '';

                    return '<label class="switch switch-success">
                        <input type="checkbox" class="switch-input changeStatus" data-type="rtl" data-id="' . $row->id . '" ' . $isActive . ' />
                        <span class="switch-toggle-slider">
                            <span class="switch-on">
                                <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                                <i class="ti ti-x"></i>
                            </span>
                        </span>
                    </label>';
                })

                ->addColumn('is_default', function ($row) {
                    $isActive = $row->is_default == 1 ? 'checked' : '';

                    return '<label class="switch switch-success">
                        <input type="checkbox" class="switch-input changeStatus" data-type="is_default" data-id="' . $row->id . '" ' . $isActive . ' />
                        <span class="switch-toggle-slider">
                            <span class="switch-on">
                                <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                                <i class="ti ti-x"></i>
                            </span>
                        </span>
                    </label>';
                })

                ->addColumn('status', function ($row) {
                    $isActive = $row->active_status == 1 ? 'checked' : '';

                    return '<label class="switch switch-success">
                        <input type="checkbox" class="switch-input changeStatus" data-type="status" data-id="' . $row->id . '" ' . $isActive . ' />
                        <span class="switch-toggle-slider">
                            <span class="switch-on">
                                <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                                <i class="ti ti-x"></i>
                            </span>
                        </span>
                    </label>';
                })

                ->addColumn('action', function ($row) {

                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">' .
                        '<a href="' . route('setting.language.setup', $row->code) . '" class="dropdown-item"><i class="ti ti-file-description" ></i> Setup</a>' .
                        '<a href="javascript:0;" class="dropdown-item edit_button" data-bs-toggle="modal" data-bs-target="#editSettingModal" data-id="' . $row->id . '"><i class="ti ti-edit" ></i> Edit</a>' .
                        '<a href="javascript:0;" class="dropdown-item setting_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                        '</div>' .
                        '</div>';

                    return $btn;
                })
                ->rawColumns(['flag','rtl','is_default','status','action',])
                ->make(true);
        }
        return view('setting.language-setting');
    }

    public function store(LanguageSettingStoreRequest $request){
        try {

           $isDefault =  $request->is_default;

           if($request->is_default == 1){
                Language::where('is_default', 1)->update(['is_default' => 0]);
           }

           if($request->status == 0  && $request->is_default == 1){
                $isDefault =  0;
                Language::where('code', 'us')->update(['is_default' => 1]);
           }

            $language = new Language();
            $language->code = strtolower($request->code);
            $language->name = $request->name;
            $language->native = $request->native;
            $language->rtl = $request->rtl_support;
            $language->is_default = $isDefault;
            $language->active_status = $request->status;
            $language->save();
            Cache::forget('active_languages');
            if ( $language && !File::exists(base_path('lang/' . $request->code))) {
                File::copyDirectory(base_path('lang/en'), base_path('lang/' . $request->code));
            }
            Toastr::success('Language Created Successfully');
        }catch (Exception $e){
            Toastr::error('Something Went Wrong!');
        }
        return back();
    }

    public function edit($id){
        $data = Language::find($id);
        if ($data){
            return response()->json(['data' => $data, 'status' => 200], 200);
        }
        return response()->json(['message' => "Settings not found"], 404);
    }

    public function update(Request $request){
        try {

            $language = Language::find($request->id);
            $language->code = $request->code;
            $language->name = $request->name;
            $language->native = $request->native;
            $language->rtl = $request->rtl_support;
            $language->save();
            Cache::forget('active_languages');
            Toastr::success('Language Update Successfully');
        }catch (Exception $e){
            Toastr::error('Something Went Wrong!');
        }
        return back();
    }

    public function delete(Request $request){
        $data = Language::find($request->id);
        try {
            if ($data->active_status == 0) {
                $data->delete();

                if (File::exists(base_path('lang/' . $data->code))) {
                    File::deleteDirectory(base_path('lang/' . $data->code));
                }
                return response()->json(['message' => 'Language Deleted Successfully', 'status' => 200], 200);
            }else{
                return response()->json(['message' => 'Please disable the status first to Delete', 'status' => 409]);
            }
            Cache::forget('active_languages');
        }catch (Exception $e){
            return response()->json(['message' => "Something Went Wrong!"], 500);
        }
    }

    public function setup($code){
        $path = base_path('lang/' . $code);

        if (File::exists($path)) {
            $data = File::files($path);
            return view('setting.language-setup', compact('data'));
        } else {
            return back();
        }
    }

    //change any status form datatable
    public function changeStatus(Request $request){


        $request->validate([
            'id' => 'required|exists:languages,id',
        ]);
        $data = Language::find($request->id);
        try {

            $condition = $request->type;

            switch ($condition) {
                case 'rtl':
                        if ($data->rtl == 1){
                            $data->rtl = 0;
                        }else{
                            $data->rtl = 1;
                        }
                    break;
                case 'is_default':

                        if ($data->active_status == 0){
                            return response()->json(['message' => 'Please enable the status first to set as default', 'status' => 409]);
                        }

                        else if ($data->is_default == 0 && $data->active_status == 1){
                            Language::where('is_default', 1)->update(['is_default' => 0]);
                            $data->is_default = 1;
                            Cache::set('locale', $data->code);
                        }

                    break;
                case 'status':
                        if ($data->active_status == 1){

                            if ($data->is_default == 1){
                                // default language can't be inactive
                                return response()->json(['message' => 'Default Language can\'t be inactive', 'status' => 409]);
                            }

                            $data->active_status = 0;
                        }else{
                            $data->active_status = 1;
                        }
                    break;
            }
            $data->save();
            Cache::forget('active_languages');
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        }catch (Exception $e){
            return response()->json(['message' => "Something Went Wrong!"], 500);
        }
    }

    public function readFile(Request $request){
        $request->validate([
            'filePath'=>'required|string'
        ]);

        $filePath = $request->input('filePath');
        if (File::exists($filePath)) {
            $fileContents = include($filePath);
            if (is_array($fileContents)) {
                return response()->json(['content' => $fileContents]);
            } else {
                return response()->json(['error' => 'File content is not an array.'], 500);
            }
        }else{
            return response()->json(['error' => 'File not found.'], 404);
        }
    }

    public function updateFile(Request $request) {
        $request->validate([
            'file-name' => 'required|string',
        ]);

        $filePath = $request->input('file-name');
        $keys = $request->input('keys');
        $values = $request->input('values');

        if (File::exists($filePath)) {
            $fileContents = include($filePath);

            $updatedContent = $fileContents;

            foreach ($keys as $index => $key) {
                $updatedContent[$key] = $values[$index] ?? '';
            }

            $content = "<?php\nreturn " . var_export($updatedContent, true) . ";\n";

            File::put($filePath, $content);

            Toastr::success("File updated successfully");
        } else {
            Toastr::error("Something Went Wrong!");
        }
        return back();
    }

    public function changeLanguage(Request $request){
        $request->validate([
            "lang" => "required|string",
        ]);

        try {
            Cache::set('locale', $request->lang);
            Language::where('is_default', 1)->update(['is_default' => 0]);
            Language::where('code', $request->lang)->update(['is_default' => 1], ['active_status' => 1]);
            Toastr::success("Language Changed Successfully");
        } catch (\Throwable $th) {
            Toastr::error("Something Went Wrong!");
        }
    }
}
