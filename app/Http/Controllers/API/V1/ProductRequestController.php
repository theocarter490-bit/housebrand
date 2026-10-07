<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductRequestResource;
use App\Models\ProductRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRequestController extends Controller
{

    public function index(Request $request)
    {
        $productRequest = ProductRequest::where('user_id', Auth::user()->id)->with('product');

        if ($request->filled('search')) {
            $productRequest->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $productRequest = $productRequest->orderBy('created_at', 'desc')->paginate(perPage());

        return sendResponse('Product request List', ProductRequestResource::collection($productRequest)->resource);
    }

    public function store(Request $request)
    {
        if ($request->user()->email_verified_at == null) {
            return sendError('Please verify your email to proceed', [], 403);
        }

        if ($request->user()->active_status == 0) {
            return sendError('Your account has been deactivated. Please contact support for more information.', [], 401);
        }

        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'product_id' => 'required|exists:products,id|integer',
            'designer' => 'sometimes|exists:shop_settings,slug',
            'variation' => 'sometimes|json',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            $productRequest = ProductRequest::where('user_id', Auth::user()->id)
                ->where('seller_id', getSellerIdByProductId($request->product_id))
                ->where('product_id', $request->product_id)
                ->where('variation_id', $request->variation_id)
                ->where('status', ProductRequest::PENDING)
                ->first();


            if (!$productRequest) {
                $productRequest = new ProductRequest();
                $productRequest->seller_id = getSellerIdByProductId($request->product_id);
                $productRequest->user_id = Auth::user()->id;
                $productRequest->designer_id = Auth::user()->designer_id;
                $productRequest->product_id = $request->product_id;
                $productRequest->variation_id = $request->variation_id;
                $productRequest->variation = json_decode($request->variation) ?? [];
                $productRequest->price = $request->price;
                $productRequest->quantity = 0;
            }

            $productRequest->quantity += $request->quantity;

            $productRequest->save();

            return sendResponse('Product has been requested');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:product_requests,id|integer',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        try {
            $productRequest = ProductRequest::where('id', $request->request_id)
                ->where('user_id', Auth::user()->id)
                ->where('status', ProductRequest::PENDING)
                ->first();
            if (!$productRequest) {
                return sendError('Product not found');
            }
            $productRequest->delete();
            return sendResponse('Product request has been deleted');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }

    }
}
