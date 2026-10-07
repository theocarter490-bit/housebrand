<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackgroundSettingsResource;
use App\Models\BackgroundSetting;
use Illuminate\Http\Request;

class BackgroundSettingsController extends Controller
{
    public function backgroundSetting($purpose){
        if ($purpose != null){
            $data = BackgroundSetting::where('is_active', 1)->where('purpose', $purpose)->get();
        }else{
            return sendError('Invalid Purpose!',null, 404);
        }

        return sendResponse('Background Settings.', BackgroundSettingsResource::collection($data), 200);
    }
}
