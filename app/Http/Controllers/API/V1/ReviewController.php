<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Product;
use App\Models\ReviewType;
use Illuminate\Http\Request;
use App\Models\DesignerReview;
use App\Models\ProductReviews;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReviewStoreRequest;
use App\Http\Resources\ReviewTypeResource;
use App\Http\Resources\ProductReviewResource;
use App\Http\Resources\DesignerReviewResource;
use App\Http\Requests\ProductReviewStoreRequest;

class ReviewController extends Controller
{

    public function index(Request $request)
    {
        try {
            $designerId = Auth::guard('sanctum')->user()->designer_id;

            $query = DesignerReview::with('reviewType', 'customer')
                ->where('designer_id', $designerId)
                ->where('active_status', 1);

            if ($request->filled('type')) {
                $query->where('review_type_id', $request->type);
            }

            $reviews = $query->latest()->paginate(perPage());
            return sendResponse('Designer Reviews.', DesignerReviewResource::collection($reviews)->resource);
        } catch (\Exception $e) {
            return sendError('Something went wrong!');
        }
    }

    public function designerReviews(Request $request)
    {
        try {
            $designer = User::where('id', getDesignerID())
                ->with('reviews', function ($query) use ($request) {
                    $query->with('reviewType', 'customer')
                        ->where('active_status', 1);
                    if ($request->filled('type')) {
                        $query->where('review_type_id', $request->type);
                    }
                })->with('shop')
                ->withCount('products', 'portfolio', 'inspiration', 'reviews')
                ->first();

            return sendResponse('Designer Reviews.', new UserResource($designer));
        } catch (\Exception $e) {
            return sendError('Something went wrong.');
        }
    }

    public function getAllTypes(Request $request)
    {
        $types = [];
        if ($request->filled('type') && $request->type == 1) {
            $types = ReviewType::whereDoesntHave('productReviews', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })->where('active_status', 1)->where('type', $request->type)->get();
        } else {
            $types = ReviewType::whereDoesntHave('shopReviews', function ($query) use ($request) {
                $query->where('designer_id', $request->designer_id);
            })->where('active_status', 1)->where('type', 0)->get();
        }
        return sendResponse('Review Types.', ReviewTypeResource::collection($types));
    }

    // store designer shop review
    public function storeReview(ReviewStoreRequest $request)
    {

        try {
            $user = Auth::guard('sanctum')->user();

            $existingReview = DesignerReview::where('customer_id', $user->id)
                ->where('review_type_id', $request->review_type_id)
                ->exists();

            if ($existingReview) {
                return sendError('You have already submitted a review of the same Type.', [], 400);
            }

            $designerReview = new DesignerReview();
            $designerReview->designer_id = $user->designer_id;
            $designerReview->review_type_id = $request->review_type_id;
            $designerReview->customer_id = $user->id;
            $designerReview->review = $request->review;
            $designerReview->rating = $request->rating;
            $designerReview->created_by = $user->id;
            $designerReview->save();
            return sendResponse('Review Submitted Successfully.');
        } catch (\Exception $e) {
            return sendError('Something went wrong!', [], 500);
        }
    }

    // store product review
    public function storeProductReview(ProductReviewStoreRequest $request)
    {
        try {

            $product = Product::find($request->product_id);
            if ($product->user_id == $request->user()->id) {
                return sendError('You cannot review your own product.', [], 400);
            }
            // checking the user existing review
            $review = ProductReviews::where([
                'product_id' => $request->product_id,
                'user_id' => $request->user()->id,
                'review_type_id' => $request->review_type_id
            ])->exists();

            if ($review) {
                return sendError('You have already submitted a review of same Type.', [], 400);
            }

            $review = new ProductReviews();
            $review->review_type_id = $request->review_type_id;
            $review->user_id = $request->user()->id;
            $review->review = $request->review;
            $review->rating = $request->rating;
            $review->product_id = $request->product_id;
            $review->save();
            return sendResponse('Review Submitted Successfully.');
        } catch (\Exception $e) {
            return sendError('Something went wrong!' . $e->getMessage(), [], 500);
        }
    }

    public function productReview(Request $request, $id)
    {
        $product = Product::find($id);
        $query = ProductReviews::with(['user', 'reviewType'])
            ->where([
                ['active_status', 1],
                ['product_id', $id]
            ]);

        if ($request->filled('type')) {
            $query->where('review_type_id', $request->type);
        }

        $reviews = $query->latest()->get();
        $avg_rating = getProductAverageRating($product);
        $total_reviews = $product->reviews()->count();
        return sendResponse('Product Reviews.', [
            'reviews' => ProductReviewResource::collection($reviews),
            'avg_rating' => $avg_rating,
            'total_reviews' => $total_reviews
        ]);
    }

}
