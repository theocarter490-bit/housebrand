<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Role;
use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Models\SearchKeyword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\AttributeResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductListResource;

class ProductController extends Controller
{


    public function list(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'array',
            'manufacturers' => 'array',
            'categories.*' => 'integer',
            'priceFrom' => 'integer',
            'priceTo' => 'integer',
            'attribute' => 'json',
            'sort_by' => 'string|in:a-z,z-a,low-high,high-low,old-new,new-old',
        ]);

        $page = $request->get('page', 1);
        $perPage = perPage();

        // 🔑 Stable cache key based on filters
        $key = 'product_list:' . md5(json_encode([
                'page' => $page,
                'per_page' => $perPage,
                'categories' => $request->categories,
                'manufacturers' => $request->manufacturers,
                'priceFrom' => $request->priceFrom,
                'priceTo' => $request->priceTo,
                'attribute' => $request->attribute,
                'sort_by' => $request->sort_by,
                'search' => $request->search,
                'designer' => $request->designer,
                'user_id' => Auth::guard('sanctum')->id(),
            ]));

        $products = getDataFromRedisAPI(
            ['product_list'],
            $key,
            function () use ($request, $perPage) {

                $productsQuery = Product::where('is_published', 1)
                    ->whereIn('user_id', getSellerIds())
                    ->with('category', 'user.shop', 'variants')
                    ->where(function ($q) {
                        $q->whereNull('category_id')
                            ->orWhereHas('category', fn($q) => $q->where('active_status', 1));
                    })
                    ->where(function ($q) {
                        $q->whereNull('brand_id')
                            ->orWhereHas('brand', fn($q) => $q->where('active_status', 1));
                    })
                    ->withCount(['wishlist' => function ($q) {
                        $q->where('user_id', Auth::guard('sanctum')->id());
                    }])
                    ->whereHas('user', function ($q) {
                        $q->where('active_status', 1)->checkSubscription();
                    });
                if ($request->filled('designer')) {
                    $shopInfo = ShopSetting::whereSlug($request->designer)->first();
                    if ($shopInfo && $shopInfo->product_setting) {
                        $productsQuery->whereJsonContains(
                            'visitors',
                            str(Auth::guard('sanctum')->id())
                        );
                    }
                }
                if ($request->has('categories')) {
                    $productsQuery->whereIn('category_id', $request->categories);
                }

                if ($request->has('manufacturers')) {
                    $productsQuery->whereIn('user_id', $request->manufacturers);
                }

                if ($request->has('priceFrom')) {
                    $productsQuery->where('unit_price', '>=', $request->priceFrom);
                }

                if ($request->has('priceTo')) {
                    $productsQuery->where('unit_price', '<=', $request->priceTo);
                }

                if ($request->filled('search')) {

                    $keywords = preg_split('/\s+/', trim($request->search));

                    $productsQuery->where(function ($q) use ($keywords) {

                        foreach ($keywords as $word) {

                            $q->orWhere(function ($q) use ($word) {

                                $q->where('name', 'like', "%{$word}%")
                                    ->orWhere('description', 'like', "%{$word}%")
                                    ->orWhereHas('category', function ($q) use ($word) {
                                        $q->where('name', 'like', "%{$word}%");
                                    })
                                    ->orWhereHas('brand', function ($q) use ($word) {
                                        $q->where('name', 'like', "%{$word}%");
                                    });
                            });
                        }
                    });
                }

                if ($request->has('attribute')) {
                    $attributes = json_decode($request->attribute, true);

                    $productsQuery->where(function ($q) use ($attributes) {
                        foreach ($attributes as $attribute) {
                            $q->whereJsonContains('choice_options', [
                                'attribute_id' => $attribute['id'],
                                'value' => $attribute['value']
                            ]);
                        }
                    });
                }
                if ($request->filled('sort_by')) {
                    match ($request->sort_by) {
                        'a-z' => $productsQuery->orderBy('name'),
                        'z-a' => $productsQuery->orderBy('name', 'desc'),
                        'low-high' => $productsQuery->orderBy('unit_price'),
                        'high-low' => $productsQuery->orderBy('unit_price', 'desc'),
                        'old-new' => $productsQuery->orderBy('created_at'),
                        'new-old' => $productsQuery->orderBy('created_at', 'desc'),
                        default => $productsQuery->orderByDesc('id'),
                    };
                } else {
                    $productsQuery->orderByDesc('id');
                }
                return ProductListResource::collection($productsQuery->paginate(perPage()))->response()->getData(true);
            }
        );

        return sendResponse(
            'All products list.',
            $products
        );
    }


    public function details($product_id)
    {

        $shop_setting = ShopSetting::where('slug', request()->designer)->first();
        $product = Product::where('id', $product_id);
        if ($shop_setting && $shop_setting->product_setting) {
            $product->whereJsonContains('visitors', str(Auth::guard('sanctum')->id()));
        }

        $product = $product->whereHas('user', function ($query) {
            $query->where('active_status', 1)
                ->checkSubscription();
        })->first();

        if ($product) {
            $checkDesignerProduct = true;
            if (request()->has('designer') && !empty(request()->designer) && request()->designer != null) {
                if ($shop_setting && $shop_setting->user_id == $product->user_id) {
                    $checkDesignerProduct = true;
                } else {
                    $checkDesignerProduct = false;
                }
            } elseif ($product->user->role_id != Role::MANUFACTURER) {
                $checkDesignerProduct = false;
            }

            $this->countProductView($product);
            $product = $product->load(['images', 'choiceOptions.values', 'variants', 'category', 'brand'])
                ->loadCount([
                    'wishlist' => function ($q) {
                        $q->where('user_id', Auth::guard('sanctum')->user()->id ?? null);
                    },
                ]);

            return sendResponse('Product details.', new ProductResource($product));

        } else {
            return sendError('Product not available.');
        }
    }

    public function filterItems(Request $request)
    {
        $attibutes = Attribute::with('values')->whereHas('values')->where('status', 1)->get();
        $manufacturer = [];
        if (empty($request->designer) && $request->designer == null) {
            $manufacturer = User::whereHas('products', function ($query) {
                $query->whereIn('user_id', getSellerIds());
            })->where('active_status', 1)->where('role_id', Role::MANUFACTURER)->where('is_subscribed', 1)->with('shop')->get();
        }
        $categories = Category::where('active_status', 1)->whereHas('products', function ($query) {
            $query->whereIn('user_id', getSellerIds());
        })->get();
        $price = DB::table('products')
            ->where(function ($q) {
                $q->whereIn('user_id', getSellerIds());
            })
            ->select(DB::raw('MIN(unit_price) AS minPrice, MAX(unit_price) AS maxPrice'))
            ->first();
        if ($price->minPrice == $price->maxPrice) {
            $price->minPrice = 0;
        }
        $data[] = [
            'name' => 'Price Range',
            'list' => [
                "minPrice" => floor($price->minPrice),
                "maxPrice" => ceil($price->maxPrice),
            ],
        ];

        $data[] = [
            'name' => 'Categories',
            'list' => CategoryResource::collection($categories),
        ];
        if (empty($request->designer) && $request->designer == null) {
            $data[] = [
                'name' => 'Manufacturers',
                'list' => UserResource::collection($manufacturer),
            ];
        }

        $data[] = [
            'name' => 'Attributes',
            'list' => AttributeResource::collection($attibutes),
        ];
        return sendResponse('All Attribute list with values.', $data);
    }

    public function relatedProducts(Product $product)
    {
        try {
            if ($product->is_published == 1) {
                $relatedProducts = Product::with('shop')->where('category_id', $product->category_id)
                    ->whereIn('user_id', getSellerIds())
                    ->whereHas('user', function ($q2) {
                        $q2->where('active_status', 1)
                            ->checkSubscription();
                    });
                if ($product->shop && $product->shop->product_setting) {
                    $relatedProducts = $relatedProducts->whereJsonContains('visitors', str(Auth::guard('sanctum')->id()));
                }
                $relatedProducts = $relatedProducts->where('id', '!=', $product->id)
                    ->with('category')
                    ->where('is_published', 1)
                    ->latest()
                    ->take(4)
                    ->get();

                if ($relatedProducts->isNotEmpty()) {
                    return sendResponse('Related products.', ProductListResource::collection($relatedProducts));
                }
            }
            return sendError('Related products not available.');
        } catch (\Exception $e) {

            return sendError('Something went wrong.');
        }
    }

//    public function relatedProducts(Product $product)
//    {
//        try {
//            if ($product->is_published != 1) {
//                return sendError('Product is not published.');
//            }
//
//            $relatedProductsQuery = Product::where('category_id', $product->category_id)
//                ->where(function ($q) {
//                    $q->whereIn('user_id', getSellerIds())
//                        ->orWhereIn('id', getSharedProductsIds());
//                })
//                ->where('id', '!=', $product->id)
//                ->where('is_published', 1)
//                ->latest()
//                ->take(4)
//                ->with(['category', 'shop']);
//
//            $authUserId = str(Auth::guard('sanctum')->id());
//            $relatedProductsQuery->whereHas('shop', function ($q) use ($authUserId) {
//                $q->where('product_setting', true)
//                    ->whereJsonContains('visitors', $authUserId);
//            });
//
//            $relatedProducts = $relatedProductsQuery->get();
//
//            if ($relatedProducts->isNotEmpty()) {
//                return sendResponse('Related products.', ProductListResource::collection($relatedProducts));
//            }
//
//            return sendError('Related products not available.');
//        } catch (\Exception $e) {
//            return sendError('Something went wrong.', $e->getMessage());
//        }
//    }


    private function countProductView(Product $product): void
    {
        $viewedProducts = Session::get('viewed_products', []);
        if (!in_array($product->id, $viewedProducts)) {
            $product->view_count += 1;
            $product->save();
            Session::push('viewed_products', $product->id);
        }
    }

    private function storeSearchKeyword(mixed $keyword): void
    {
        $searchKeyword = SearchKeyword::where('keyword', $keyword)->first();
        if ($searchKeyword) {
            $searchKeyword->increment('count');
        } else {
            SearchKeyword::create(['keyword' => $keyword, 'count' => 1]);
        }
    }
}
