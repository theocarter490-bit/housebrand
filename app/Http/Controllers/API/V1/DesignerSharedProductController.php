<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SharedProductResource;
use App\Models\DesignerSharedProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DesignerSharedProductController extends Controller
{

    public function store(Request $request)
    {

        if ($request->user()->email_verified_at == null){
            return sendError('Please verify your email to proceed', [], 403);
        }

        if ($request->user()->active_status == 0){
            return sendError('Your account has been deactivated. Please contact support for more information.', [], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id|integer',
            'designer' => 'sometimes|exists:shop_settings,slug',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {

            $seller_id = getSellerIdByProductId($request->product_id);

            $designerSharedProduct = DesignerSharedProduct::where('designer_id', getUserId())
                ->where('seller_id', $seller_id)
                ->where('product_id', $request->product_id)
                ->first();
            if (!$designerSharedProduct) {
                $designerSharedProduct = new designerSharedProduct();
                $designerSharedProduct->designer_id = getUserId();
                $designerSharedProduct->seller_id = $seller_id;
                $designerSharedProduct->product_id = $request->product_id;
                $designerSharedProduct->save();
                return sendResponse('Request have been sent');
            } else {
                return sendError('This product has already been requested.', [], 409);
            }

        } catch (Exception $e) {
            return sendError('Something went wrong', [], 500);
        }
    }

    public function index(Request $request)
    {
        $sharedProduct = DesignerSharedProduct::where('designer_id', Auth::user()->id)->whereHas('product')->with(['product' => function ($query) use ($request) {
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->get('search') . '%');
            }
        }, 'seller.shop'])->paginate(perPage());


        return sendResponse('Shared Product List.', SharedProductResource::collection($sharedProduct)->resource);
    }
}
