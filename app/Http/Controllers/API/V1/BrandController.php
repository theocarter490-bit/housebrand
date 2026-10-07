<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    // index
    public function list(Request $request)
    {
        $brands = Brand::whereHas('products', function ($q) {
            $q->whereIn('user_id', getSellerIds())
                ->whereHas('user', function ($query) {
                    $query->where('active_status', 1)
                        ->checkSubscription();
                });
        })
            ->where('active_status', 1)
            ->paginate(perPage());

        return sendResponse('All brand list.', BrandResource::collection($brands)->resource);
    }
}
