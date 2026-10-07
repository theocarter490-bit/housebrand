<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\ShopSetting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SiteLogoStoreRequest;
use App\Http\Requests\SocialLinkStoreRequest;
use App\Http\Requests\SystemInfoStoreRequest;

class ShopSettingController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        $setting = shopSetting();
        return view('setting.shop-setting', compact('setting'));
    }

    public function storeSystemInfo(SystemInfoStoreRequest $request)
    {

        try {
            $shop_setting = ShopSetting::where('user_id', getUserId())->first();
            if (!$shop_setting) {
                $shop_setting = new ShopSetting();
                $shop_setting->user_id = getUserId();
            }
            $shop_setting->shop_name = $request->shop_name;
            $shop_setting->home_slider_style = $request->styleList ?? 'image';
            $shop_setting->slug = Str::slug($request->shop_name);
            $shop_setting->location = $request->address;
            $shop_setting->phone = $request->phone;
            $shop_setting->email = $request->email;
            $shop_setting->map_location = $request->map_location;

            $shop_setting->save();
            $this->forgetCache($shop_setting->user_id, $shop_setting->slug);
            return response()->json(['message' => 'Shop Setting Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function storeSiteLogo(SiteLogoStoreRequest $request)
    {

        $general_setting = ShopSetting::where('user_id', getUserId())->first();

        try {
            if ($request->hasFile('light_logo')) {
                $path = $this->uploadFile($request->file('light_logo'), 'designer/' . Auth::user()->id . '/icon');

                if ($general_setting->logo) {
                    $this->deleteFile($general_setting->logo);
                }
                $general_setting->logo = $path;
            }
            if ($request->hasFile('banner')) {

                $path = $this->uploadFile($request->file('banner'), 'designer/' . Auth::user()->id . '/icon');
                if ($general_setting->banner) {
                    $this->deleteFile($general_setting->banner);
                }
                $general_setting->banner = $path;
            }
            if ($request->hasFile('favicon')) {

                $path = $this->uploadFile($request->file('favicon'), 'designer/' . Auth::user()->id . '/icon');
                if ($general_setting->favicon) {
                    $this->deleteFile($general_setting->favicon);
                }
                $general_setting->favicon = $path;
            }
            if ($request->hasFile('loader')) {

                $path = $this->uploadFile($request->file('loader'), 'designer/' . Auth::user()->id . '/icon', 'default');
                if ($general_setting->loader) {
                    $this->deleteFile($general_setting->loader);
                }
                $general_setting->loader = $path;
            }

            $general_setting->save();
            $this->forgetCache($general_setting->user_id, $general_setting->designer);
            return response()->json(['message' => 'Shop Logo Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function storeSocialLink(SocialLinkStoreRequest $request)
    {

        try {
            $shop_setting = ShopSetting::where('user_id', getUserId())->first();

            $shop_setting->twitter_url = $request->twitter;
            $shop_setting->facebook_url = $request->facebook;
            $shop_setting->instagram_url = $request->instagram;
            $shop_setting->linkedin = $request->linkedin;
            $shop_setting->youtube_url = $request->youtube;
            $shop_setting->tiktok_url = $request->tiktok;

            $shop_setting->save();
            $this->forgetCache($shop_setting->user_id, $shop_setting->designer);
            return response()->json(['message' => 'Social Link Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function storeTermsPolices(Request $request)
    {
        try {
            $shop_setting = ShopSetting::where('user_id', getUserId())->first();
            $shop_setting->shipping_policy = $request->shipping_policy;
            $shop_setting->return_policy = $request->return_policy;
            $shop_setting->disclaimer = $request->disclaimer;
            $shop_setting->update();
            $this->forgetCache($shop_setting->user_id, $shop_setting->designer);
            return response()->json(['message' => 'Terms & Polices Updated', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 403], 403);
        }
    }

    public function storeEmergencyNotice(Request $request)
    {
        try {
            $shop = shopSetting();
            $shop->emergency_notice = $request->emergency_notice;
            $shop->save();
            $this->forgetCache($shop->user_id, $shop->slug);
            return response()->json(['message' => 'Emergency Notice Updated', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeEmergencyNoticeStatus()
    {
        try {
            $shop = shopSetting();
            $shop->emergency_notice_status = !$shop->emergency_notice_status;
            $shop->save();
            $this->forgetCache($shop->user_id, $shop->slug);
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeProductSettingStatus()
    {
        try {
            $shop = shopSetting();
            $shop->product_setting = !$shop->product_setting;
            $shop->save();
            $this->forgetCache($shop->user_id, $shop->slug);
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeShopStatus()
    {
        try {
            $shop = shopSetting();
            $shop->shop_status = !$shop->shop_status;
            $shop->save();
            return response()->json(['message' => 'Shop Status Updated Successfully', 'status' => 200]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    private function forgetCache(mixed $id, $slug): void
    {
        Cache::forget("shop_Setting_$id");
        Cache::forget("shop_Setting_$slug");
        removeDataFromRedisAPI(['shop_setting'], $id);
        removeDataFromRedisAPI(['shop_setting'], $slug);
    }


    public function promotionStore(Request $request)
    {
        try {
            $shop = shopSetting();
            $shop->promotional_title = $request->title;
            $shop->promotional_description = $request->description;
            $shop->promotional_video_link = $request->link;
            $shop->save();
            $this->forgetCache($shop->user_id, $shop->slug);
            return response()->json(['message' => 'Promotion Details Updated Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }
}
