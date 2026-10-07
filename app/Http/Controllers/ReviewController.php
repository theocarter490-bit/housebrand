<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Product;
use App\Models\ReviewType;
use Illuminate\Http\Request;
use App\Models\DesignerReview;
use App\Models\ProductReviews;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = DesignerReview::with([
            'reviewType',
            'customer:id,name,email,phone',
            'designer.shop'
        ])->isClient('designer_id')->latest();

        if ($request->filled('search')) {
            $searchQuery = '%' . $request->search . '%';

            $query->where(function ($subQuery) use ($searchQuery) {
                $subQuery->where('review', 'like', $searchQuery)
                    ->orWhereHas('designer.shop', function ($q) use ($searchQuery) {
                        $q->where('shop_name', 'like', $searchQuery)
                            ->orWhere('email', 'like', $searchQuery);
                    })
                    ->orWhereHas('customer', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', $searchQuery)
                            ->orWhere('email', 'like', $searchQuery);
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('review_type_id', $request->type);
        }

        if ($request->filled('status_type')) {
            $query->where('active_status', $request->status_type);
        }

        $reviewTypes = ReviewType::where('active_status', 1)
            ->where('type', 0)
            ->get();

        $reviews = $query->paginate(perPage());
        return view('designer-reviews.index', compact('reviews', 'reviewTypes'));
    }

    public function changeStatus(Request $request)
    {
        try {
            $data = DesignerReview::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function productReviews(Request $request)
    {
        $userId = getUserId();

        $productReviewsQuery = ProductReviews::with('product.shop', 'user', 'reviewType');

        if ($userId != Role::SUPER_ADMIN) {
            $productReviewsQuery->whereHas('product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        if ($request->filled('search')) {
            $query = $request->search;
            $productReviewsQuery->whereHas('product', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            });
        }

        if ($request->filled('type')) {
            $productReviewsQuery->where('review_type_id', $request->type);
        }

        if ($request->filled('status_type')) {
            $productReviewsQuery->where('active_status', $request->status_type);
        }

        $reviews = $productReviewsQuery->latest()->paginate();

        $reviewTypes = ReviewType::where('active_status', 1)
            ->where('type', 1)
            ->get();

        return view('product.review.index', compact('reviews', 'reviewTypes'));
    }


    public function productReviewsDetails(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $productUrl = '#';
        $shopUrl = '#';
        if ($product->user->role_id == Role::DESIGNER) {
            $productUrl = env('APP_FRONTEND_URL') . '/designer/' . $product->shop->slug . '/product/' . $product->id;
            $shopUrl = env('APP_FRONTEND_URL') . '/designer/' . $product->shop->slug;
        } else {
            $productUrl = env('APP_FRONTEND_URL') . '/product/' . $product->id;
        }

        $productReviewsQuery = ProductReviews::with('product', 'user', 'reviewType')
            ->where('product_id', $id);

        if ($request->filled('search')) {
            $query = $request->search;
            $productReviewsQuery->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            });
        }

        if ($request->filled('type')) {
            $productReviewsQuery->where('review_type_id', $request->type);
        }

        if ($request->filled('status_type')) {
            $productReviewsQuery->where('active_status', $request->status_type);
        }

        $reviews = $productReviewsQuery->latest()->paginate();

        $reviewTypes = ReviewType::where('active_status', 1)
            ->where('type', 1)
            ->get();

        return view('product.review.details', compact('reviews', 'reviewTypes', 'product', 'productUrl', 'shopUrl'));
    }


    public function productReviewStatusChange(Request $request)
    {
        try {
            $data = ProductReviews::findOrFail($request->id);
            $data->active_status = !$data->active_status;
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }
}
