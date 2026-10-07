<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Page;
use App\Models\Role;
use App\Models\Slider;
use Stripe\Collection;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SliderResource;

class SliderController extends Controller
{

    public function sliderList($type = null)
    {
        try {
            // Get shop setting
            $shop_setting = ShopSetting::query();
            if (request()->has('designer') && !empty(request()->designer)) {
                $shop_setting = $shop_setting->where('slug', request()->designer)->first();
            } else {
                $shop_setting = $shop_setting->where('id', 1)->first();
            }

            // Prepare the query
            $query = Slider::with(['products' => function ($q) {
                $q->where('is_published', 1)->with('images');
            }])
                ->where('active_status', 1)
                ->where('type', $type)
                ->where('user_id', getDesignerID());

            if ($type == 2 || $shop_setting->home_slider_style != 'video') {
                $query = $query->where('file_type', 0);
            } else {
                $query = $query->where('file_type', 1)->latest()->take(1);
            }

            // Fetch from cache or execute and then return the collection
            $sliders = getDataFromRedisAPI(['slider_list', getDesignerID()], "type_" . $type, function () use ($query) {
                return SliderResource::collection($query->get())->resolve();
            });

            return sendResponse('Slider List.', ['sliders' => $sliders]);
        } catch (\Throwable $th) {
            return sendError('Something went wrong.', $th->getMessage());
        }
    }
}
