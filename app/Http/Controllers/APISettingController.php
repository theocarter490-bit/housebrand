<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class APISettingController extends Controller
{
    // index
    public function index()
    {
        $setting = GeneralSetting::first();
        return view('setting.api-setting.index',compact('setting'));
    }

}
