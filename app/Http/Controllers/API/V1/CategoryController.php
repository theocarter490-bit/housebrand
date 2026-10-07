<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\ShopSetting;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // index
    public function list(Request $request)
    {
        $categories = CategoryResource::collection(Category::withCount(['products' => function ($q) use ($request) {
            $q->whereIn('user_id', getSellerIds())->where('is_published', 1)
                ->whereHas('user', function ($query) {
                    $query->where('active_status', 1)
                        ->checkSubscription();
                });
            if ($request->filled('designer')) {
                $shopInfo = ShopSetting::whereSlug($request->designer)->first();

                if ($shopInfo && $shopInfo->product_setting) {
                    $q->whereJsonContains('visitors', str(\Auth::guard('sanctum')->id()));
                }
            }
        }])
            ->having('products_count', '>', 0)
            ->where('active_status', 1)
            ->paginate(perPage()))
            ->resource;

        $border_shape = globalSetting('category_design')->value == 0 ? json_decode(globalSetting('category_design')->options)[0] : json_decode(globalSetting('category_design')->options)[1];

        return sendResponse('All category list.', ['categories' => $categories, 'category_design' => $border_shape]);
    }

    public function categoryWithProduct(Request $request)
    {
        $categories = Category::where('active_status', 1)
            ->whereHas('products', function ($q) use ($request) {
                $q->whereIn('user_id', getSellerIds())
                    ->where('is_published', 1);
                if ($request->filled('designer')) {
                    $shopInfo = ShopSetting::whereSlug($request->designer)->first();

                    if ($shopInfo && $shopInfo->product_setting) {
                        $q->whereJsonContains('visitors', str(\Auth::guard('sanctum')->id()));
                    }
                }
            })
            ->with('products', function ($q) use ($request) {
                $q->whereIn('user_id', getSellerIds())
                    ->where('is_published', 1);
                if ($request->filled('designer')) {
                    $shopInfo = ShopSetting::whereSlug($request->designer)->first();

                    if ($shopInfo && $shopInfo->product_setting) {
                        $q->whereJsonContains('visitors', str(\Auth::guard('sanctum')->id()));
                    }
                }
            })
            ->paginate(perPage());
        return sendResponse('All category list with products.', CategoryResource::collection($categories)->resource);
    }
}
