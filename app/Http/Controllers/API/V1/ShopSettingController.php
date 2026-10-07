<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FooterWidgetResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\PromotionDetailsResource;
use App\Http\Resources\ShopSettingResource;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodStatus;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ShopSettingController extends Controller
{
    //
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designer' => 'sometimes|required|exists:shop_settings,slug',
        ]);

        if ($validator->fails()) {
            return sendError($validator->errors()->first());
        }
        $slug = ($request->has('designer') && !empty($request->designer))
            ? $request->designer
            : 'default_shop';

        $data = getDataFromRedisAPI(['shop_setting'], $slug, function () use ($slug) {
            $query = ShopSetting::with('seller');

            if ($slug === 'default_shop') {
                $shop_setting = $query->where('id', 1)->first();
            } else {
                $shop_setting = $query->where('slug', $slug)->first();
            }

            if (!$shop_setting) return null;

            return (new ShopSettingResource($shop_setting))->resolve();
        });

        if (!$data) {
            return sendError('Shop setting not found.');
        }
        return sendResponse('Shop Setting', $data);
    }

    public function getActivePaymentMethods()
    {
        $activePaymentMethods = PaymentMethodStatus::with('paymentMethod')->where('user_id', getDesignerID())->where('active_status', 1)->where('setup_status', 1)->get();
        return sendResponse('Active Payment Methods', PaymentMethodResource::collection($activePaymentMethods));
    }
}
