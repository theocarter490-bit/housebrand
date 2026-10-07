<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralSettingSystemInfoStoreRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Currencies;
use App\Models\DateFormat;
use App\Models\GeneralSetting;
use App\Models\TimeZone;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\Cache;

class GeneralSettingController extends Controller
{
    //
    use FileUploadTrait;

    public function index()
    {
        $currences = Currencies::all();
        $time_zones = TimeZone::all();
        $date_formats = DateFormat::all();
        $setting = generalSetting();

        return view('setting.general-setting', compact('setting', 'currences', 'time_zones', 'date_formats'));
    }

    public function storeSystemInfo(GeneralSettingSystemInfoStoreRequest $request)
    {

        try {
            $general_setting = GeneralSetting::first();

            $general_setting->site_name = $request->siteName;
            $general_setting->site_title = $request->siteTitle;
            $general_setting->copyright_text = $request->copy_right;
            $general_setting->time_zone_id = $request->timeZone;
            $general_setting->currency_id = $request->currence;
            $general_setting->date_format_id = $request->dateFormat;
            $general_setting->save();

            $this->updateEnv(['APP_TIMEZONE' => $general_setting->timezone->time_zone]);
            Cache::forget('general_Setting');
            return response()->json(['message' => 'General Setting Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }


    protected function updateEnv(array $data)
    {
        $envPath = base_path('.env');
        if (File::exists($envPath)) {

            $env = File::get($envPath);

            foreach ($data as $key => $value) {
                if (strpos($env, "{$key}=") !== false) {
                    $env = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $env);
                } else {
                    $env .= "\n{$key}=\"{$value}\"";
                }
            }
            File::put($envPath, $env);
        }
    }
}
