<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishListResource;
use App\Models\Product;
use App\Models\Wishlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{

    public function list(Request $request)
    {

        $wishlist = Wishlist::where('user_id', Auth::guard('sanctum')->user()->id);
        if ($request->has('search') && $request->search != null) {
            $wishlist = $wishlist->whereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $wishlist = $wishlist->with('product')->paginate(perPage());

        return sendResponse('Wishlist List.', WishListResource::collection($wishlist)->resource);
    }

    public function toggle(Request $request)
    {
        if ($request->user()->email_verified_at == null) {
            return sendError('Please verify your email to proceed', [], 403);
        }

        if ($request->user()->active_status == 0) {
            return sendError('Your account has been deactivated. Please contact support for more information.', [], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }

        try {

            $product = Product::find($request->product_id);
            if (!$product) {
                return sendError('Product not found', [], 404);
            }

            // Check if user owns the product
            if ($product->user_id == Auth::guard('sanctum')->user()->id) {
                return sendError('You are the owner of this product', [], 403);
            }

            $wishlist = Wishlist::where('user_id', Auth::user()->id)
                ->where('product_id', $request->product_id)
                ->first();
            removeDataFromRedisAPI(['product_list']);
            if ($wishlist) {
                $wishlist->delete();
                return sendResponse('Removed from wishlist');
            } else {
                $wishlist = new Wishlist();
                $wishlist->user_id = Auth::user()->id;
                $wishlist->product_id = $request->product_id;
                $wishlist->save();
                return sendResponse('Added to wishlist');
            }


        } catch (Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        try {
            $data = Wishlist::where('id', $request->id)
                ->where('user_id', Auth::user()->id)->first();
            removeDataFromRedisAPI(['product_list']);
            if ($data) {
                $data->delete();
                return sendResponse('Remove from wishlist');
            } else {
                return sendError('Wishlist not found!');
            }
        } catch (Exception $e) {
            return sendError('Something went wrong!');
        }
    }
}
