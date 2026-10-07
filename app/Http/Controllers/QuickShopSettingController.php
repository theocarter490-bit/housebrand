<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShopSettingResource;
use App\Http\Traits\FileUploadTrait;
use App\Models\Category;
use App\Models\ColorPalette;
use App\Models\ColorTheme;
use App\Models\EmailSetting;
use App\Models\FooterWidget;
use App\Models\Gallery;
use App\Models\PaymentMethodStatus;
use App\Models\Product;
use App\Models\ShopSetting;
use App\Models\Slider;
use App\Models\SpecialSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuickShopSettingController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        $emailSetting = EmailSetting::byShop()->first();
        $paymentMethodStatus = PaymentMethodStatus::where('active_status', 1)->where('user_id', getUserId())->get();
        return view('setting.quick-shop-setup', compact('emailSetting', 'paymentMethodStatus'));
    }

    public function heroSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'string|nullable',
            'file_type' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            $data = new Slider();
            $data->title = $request->title;
            $data->description = $request->description;
            $data->user_id = getUserId();
            $data->type = 0;
            $data->active_status = 1;
            $data->file_type = $request->file_type;
            if ($request->hasFile('image')) {
                $data->image = $this->uploadFile($request->file('image'), 'slider', 'default');
            }
            $productList = [];
            $data->product_ids = $productList;
            $data->save();
            return response()->json(['message' => 'Hero section successfully saved', 'success' => true], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => "Something went wrong", 'success' => false], 500);
        }

    }

    public function category()
    {
        try {
            $category = Category::byShop('created_by')->get();
            return response()->json(['category' => $category, 'success' => true], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => "Something went wrong", 'success' => false], 500);
        }

    }

    public function sectionContent(Request $request)
    {
        $data = ShopSetting::where('user_id', getUserId())->first();
        if ($data) {
            $content = json_decode($data->section_content, true);

            $content[$request->section]['display_control'] = $request->display_control;
            $content[$request->section]['label'] = $request->label;
            $content[$request->section]['description'] = $request->description;

            $data->section_content = json_encode($content);
            $data->save();
        }
        $data->save();
    }

    public function getShopSetting()
    {
        $shopSetting = ShopSetting::where('user_id', getUserId())->first();
        return response()->json(['shopSetting' => new ShopSettingResource($shopSetting), 'success' => true], 200);
    }

    public function getColorTheme()
    {
        $colorTheme = ColorTheme::where('user_id', getUserId())->where('active_status', 1)->where('type', 0)->first();
        return response()->json(['colorTheme' => $colorTheme, 'success' => true], 200);
    }

    public function getFooter()
    {
        $widgets = FooterWidget::with(['pages' => function ($query) {
            $query->where('user_id', getUserId())->where('active_status', 1);
        }])->where('active_status', 1)->orderBy('serial', 'ASC')->get();
        return response()->json(['widgets' => $widgets, 'success' => true], 200);
    }

    public function shopSettingPercentage()
    {
        $userId = getUserId();
        $moduleCompleted = 0;
        $totalModule = 8;

        $shopSetting = ShopSetting::where('user_id', $userId)->first();

        if ($shopSetting) {
            $fields = ['shop_name', 'shipping_policy', 'disclaimer', 'return_policy', 'logo', 'favicon', 'banner'];

            $filledFields = collect($fields)->filter(function ($field) use ($shopSetting) {
                return !empty($shopSetting->$field);
            })->count();

            if ($filledFields === count($fields)) {
                $moduleCompleted += 1;
            }
        }

        $hasCategory = Category::where('created_by', $userId)->exists();
        if ($hasCategory) {
            $moduleCompleted += 1;
        }

        $hasInspiration = SpecialSection::where('type', 2)->where('user_id', $userId)->exists();
        if ($hasInspiration) {
            $moduleCompleted += 1;
        }

        $hasPortfolio = SpecialSection::where('type', 1)->where('user_id', $userId)->exists();
        if ($hasPortfolio) {
            $moduleCompleted += 1;
        }

        $hasProduct = Product::where('user_id', $userId)->exists();
        if ($hasProduct) {
            $moduleCompleted += 1;
        }

        $hasGallery = Gallery::where('user_id', $userId)->exists();
        if ($hasGallery) {
            $moduleCompleted += 1;
        }

        $hasPaymentMethodStatus = PaymentMethodStatus::where('user_id', $userId)
            ->where('setup_status', 1)
            ->exists();
        if ($hasPaymentMethodStatus) {
            $moduleCompleted += 1;
        }

        $hasColorTheme = ColorTheme::where('user_id', $userId)->exists();
        if ($hasColorTheme) {
            $moduleCompleted += 1;
        }

        $percentage = round(($moduleCompleted / $totalModule) * 100);
        return response()->json([
            'percent' => $percentage,
            'modules_completed' => $moduleCompleted,
            'total_modules' => $totalModule,
            'success' => true,
        ], 200);
    }


}
