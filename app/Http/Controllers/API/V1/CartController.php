<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductRequest;
use App\Models\ShopSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{


    public function index(Request $request)
    {
        $cartQuery = Cart::with('product')->where('user_id', Auth::user()->id);
        if ($request->filled('search')) {
            $cartQuery->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $cart = $cartQuery->orderBy('created_at', 'desc')->get();
        return sendResponse('Cart List', CartResource::collection($cart));
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
            $cart = Cart::where('user_id', Auth::user()->id)
                ->where('seller_id', getSellerIdByProductId($request->product_id))
                ->where('product_id', $request->product_id)
                ->where('variation_id', $request->variation_id)
                ->first();

            if (!$cart) {
                $cart = new Cart();
                $cart->seller_id = getSellerIdByProductId($request->product_id);
                $cart->user_id = Auth::user()->id;
                $cart->product_id = $request->product_id;
                $cart->variation_id = $request->variation_id;
                $cart->variation = json_decode($request->variation) ?? [];
                $cart->price = $request->price;
            }

            $cart->quantity += $request->quantity;

            $cart->save();

            return sendResponse('Cart Updated Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:carts,id|integer',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        try {
            Cart::where('id', $request->cart_id)->delete();

            return sendResponse('Product Deleted from cart list');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|array',
            'cart_id.*' => 'required|exists:carts,id|integer',
            'quantity' => 'required|array',
            "quantity.*" => "required|int|min:1",
        ]);

        if ($request->user()->email_verified_at == null) {
            return sendError('Please verify your email to proceed', [], 403);
        }

        if ($request->user()->active_status == 0) {
            return sendError('Your account has been deactivated. Please contact support for more information.', [], 401);
        }

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {
            foreach ($request->cart_id as $key => $cart_id) {
                $cart = Cart::find($cart_id);
                if ($cart) {
                    $cart->quantity = $request->quantity[$key];
                    $cart->save();
                }
            }
            return sendResponse('Cart Updated Successfully');
        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }
}
