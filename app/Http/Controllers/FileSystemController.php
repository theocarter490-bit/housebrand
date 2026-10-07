<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileSystemCredentialStoreRequest;
use App\Models\FileSystem;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;

class FileSystemController extends Controller
{
    public function index(){
        $settings = GlobalSetting::where('type', 'file_system')->get();
        $credentials = FileSystem::where('type', 's3')->pluck('value','key');
        return view('setting.file-system.index', compact('settings','credentials'));
    }

    public function storeCredentials(FileSystemCredentialStoreRequest $request){

        try {
            $existingCredentials = FileSystem::where('type', 's3')->pluck('value', 'key');
            foreach ($existingCredentials as $key => $value) {
                if ($request->has($key)) {
                    FileSystem::where('type', 's3')->where('key', $key)
                        ->update(['value' => $request->$key]);
                }
                putEnvConfigration($key, $value);
            }
            Toastr::success('Credentials saved successfully', 'Success');
        }catch (\Exception $e){
            Toastr::error($e->getMessage(), 'error');
        }
        return redirect()->back();
    }
}
